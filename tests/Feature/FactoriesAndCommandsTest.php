<?php

namespace Tests\Feature;

use App\Http\Resources\ContentOptionResource;
use App\Models\Category;
use App\Models\Collaborator;
use App\Models\CollaboratorProject;
use App\Models\Content;
use App\Models\ContentOption;
use App\Models\Group;
use App\Models\Page;
use App\Models\Report;
use App\Models\Suggestion;
use App\Models\SuggestionOption;
use App\Models\Type;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FactoriesAndCommandsTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_twelve_model_factories_create_valid_records_and_relationships(): void
    {
        $regularUser = User::factory()->create();
        $adminUser = User::factory()->admin()->create();

        $this->assertSame(2, $regularUser->role_id);
        $this->assertNotEmpty($regularUser->nick);
        $this->assertSame(1, $adminUser->role_id);
        $this->assertTrue($adminUser->isAdmin);

        $type = Type::factory()->create(['slug' => 'quiz-factory']);
        $group = Group::factory()->create(['type_id' => $type->id]);
        $category = Category::factory()->create();

        $content = Content::factory()->create([
            'user_id' => $regularUser->id,
            'group_id' => $group->id,
        ]);
        $content->categories()->syncWithoutDetaching([$category->id]);

        $adultContent = Content::factory()->adult()->create(['group_id' => $group->id]);
        $aiContent = Content::factory()->ai()->create(['group_id' => $group->id]);
        $anonContent = Content::factory()->anonymous('anon_factory')->create(['group_id' => $group->id]);

        $this->assertTrue($content->fresh()->categories->contains('id', 1));
        $this->assertTrue((bool) $adultContent->is_adult);
        $this->assertTrue((bool) $aiContent->is_ai);
        $this->assertNull($anonContent->user_id);
        $this->assertSame('@anon_factory', $anonContent->uploader);

        $option = ContentOption::factory()->correct()->create([
            'content_id' => $content->id,
            'image_path' => 'content-option-images/sample.webp',
        ]);
        $this->assertTrue((bool) $option->is_correct);

        $resourcePayload = (new ContentOptionResource($option))->toArray(Request::create('/'));
        $this->assertStringContainsString('content-option-images/sample.webp', (string) $resourcePayload['urlImage']);

        $suggestion = Suggestion::factory()->create([
            'type_id' => $type->id,
            'group_id' => $group->id,
        ]);
        $approvedSuggestion = Suggestion::factory()->approved()->create([
            'type_id' => $type->id,
            'group_id' => $group->id,
        ]);
        $suggestionOption = SuggestionOption::factory()->correct()->create([
            'suggestion_id' => $suggestion->id,
        ]);

        $this->assertNull($suggestion->approved_at);
        $this->assertNotNull($approvedSuggestion->approved_at);
        $this->assertTrue($suggestion->fresh()->categories->contains('id', 1));
        $this->assertSame($suggestion->id, $suggestionOption->suggestion->id);

        $collaborator = Collaborator::factory()->create(['user_id' => $regularUser->id]);
        $publishedProject = CollaboratorProject::factory()->create(['collaborator_id' => $collaborator->id]);
        $draftProject = CollaboratorProject::factory()->draft()->create(['collaborator_id' => $collaborator->id]);

        $this->assertSame($collaborator->id, $publishedProject->collaborator->id);
        $this->assertSame('published', $publishedProject->status);
        $this->assertSame('draft', $draftProject->status);

        $report = Report::factory()->create([
            'user_id' => $regularUser->id,
            'reportable_type' => Content::class,
            'reportable_id' => $content->id,
        ]);
        $this->assertSame($content->id, $report->reportable->id);

        $publishedPage = Page::factory()->create();
        $draftPage = Page::factory()->draft()->create();

        $this->assertSame('published', $publishedPage->status);
        $this->assertSame('draft', $draftPage->status);
    }

    public function test_stats_update_command_populates_platform_cache_keys(): void
    {
        Cache::flush();

        $type = Type::factory()->create(['slug' => 'chistes']);
        $group = Group::factory()->create(['type_id' => $type->id]);
        $user = User::factory()->create(['nick' => 'stats_author']);

        Content::factory()->create([
            'user_id' => $user->id,
            'group_id' => $group->id,
            'uploaded_by' => null,
        ]);
        Content::factory()->anonymous('guest_stats')->create([
            'group_id' => $group->id,
        ]);

        Suggestion::factory()->create(['type_id' => $type->id, 'group_id' => $group->id]);
        Suggestion::factory()->approved()->create(['type_id' => $type->id, 'group_id' => $group->id]);

        $this->artisan('stats:update')->assertExitCode(0);

        $this->assertSame(1, Cache::get('suggestions_pending_count'));
        $this->assertSame(1, Cache::get('suggestions_accepted_count'));
        $this->assertSame(2, Cache::get('contents_count'));
        $this->assertGreaterThanOrEqual(2, Cache::get('users_active_total'));
        $this->assertIsArray(Cache::get('users_more_active_10'));
        $this->assertIsArray(Cache::get('users_more_active_20'));
        $this->assertIsArray(Cache::get('types_and_groups_and_categories_count'));
    }

    public function test_publish_social_command_publishes_only_public_safe_content(): void
    {
        config(['services.social_publisher.url' => 'https://social.example.test/publish']);

        Http::fake([
            'https://social.example.test/publish' => Http::response(['success' => true], 200),
        ]);

        $type = Type::factory()->create(['slug' => 'chistes']);
        $group = Group::factory()->create(['type_id' => $type->id]);

        $adultContent = Content::factory()->adult()->create([
            'group_id' => $group->id,
            'last_social_published' => null,
        ]);

        $safeContent = Content::factory()->create([
            'group_id' => $group->id,
            'title' => 'Contenido seguro para redes',
            'last_social_published' => null,
        ]);

        $this->artisan('content:publish-social')->assertExitCode(0);

        Http::assertSentCount(1);
        Http::assertSent(fn ($request) => ($request['title'] ?? null) === $safeContent->title);

        $this->assertNotNull($safeContent->fresh()->last_social_published);
        $this->assertNull($adultContent->fresh()->last_social_published);
    }

    public function test_sitemap_generate_command_writes_public_urls_and_excludes_adult_groups(): void
    {
        $type = Type::factory()->create(['name' => 'Chistes', 'slug' => 'chistes']);
        $safeGroup = Group::factory()->create(['type_id' => $type->id, 'slug' => 'chistes-cortos']);

        DB::table('groups')->updateOrInsert(
            ['id' => 4],
            [
                'type_id' => $type->id,
                'title' => 'Adultos',
                'slug' => 'grupo-adultos-4',
                'description' => 'Grupo excluido',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        $this->syncPostgresSequence('groups', 'id', 100);

        $publicCategory = Category::factory()->create(['slug' => 'categoria-publica']);

        $safeContent = Content::factory()->create(['group_id' => $safeGroup->id]);
        $safeContent->categories()->attach($publicCategory->id);

        $adultContent = Content::factory()->adult()->create(['group_id' => 4]);

        $publishedPage = Page::factory()->create(['slug' => 'guia-publica']);
        $draftPage = Page::factory()->draft()->create(['slug' => 'borrador-privado']);

        $collaborator = Collaborator::factory()->create(['nick' => 'colab_sitemap']);
        $project = CollaboratorProject::factory()->create([
            'collaborator_id' => $collaborator->id,
            'slug' => 'proyecto-sitemap',
            'status' => 'published',
        ]);

        $outputPath = sys_get_temp_dir().'/jaja_sitemap_'.uniqid('', true).'.xml';

        try {
            $this->artisan('sitemap:generate', [
                '--force' => true,
                '--output' => $outputPath,
            ])->assertExitCode(0);

            $this->assertFileExists($outputPath);
            $xml = (string) file_get_contents($outputPath);

            $this->assertStringContainsString('/grupo/chistes-cortos/random', $xml);
            $this->assertStringContainsString('/categoria/categoria-publica/random', $xml);
            $this->assertStringContainsString('/pagina/'.$publishedPage->slug, $xml);
            $this->assertStringContainsString('/colaborador/'.$collaborator->nick.'/project/'.$project->slug, $xml);

            $this->assertStringNotContainsString('/grupo/grupo-adultos-4/random', $xml);
            $this->assertStringNotContainsString($draftPage->slug, $xml);
            $this->assertNotNull($adultContent->id);
        } finally {
            @unlink($outputPath);
        }
    }
}
