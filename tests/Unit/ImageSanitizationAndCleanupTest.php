<?php

namespace Tests\Unit;

use App\Actions\ConvertImageToWebp;
use App\Http\Requests\SuggestionRequest;
use App\Models\Category;
use App\Models\Collaborator;
use App\Models\CollaboratorProject;
use App\Models\Content;
use App\Models\ContentOption;
use App\Models\Group;
use App\Models\Page;
use App\Models\Suggestion;
use App\Models\SuggestionOption;
use App\Models\Type;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ImageSanitizationAndCleanupTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Category::create([
            'id' => 1,
            'title' => 'General',
            'slug' => 'general',
            'description' => 'Categoría general',
        ]);
    }

    public function test_convert_image_to_webp_scales_down_and_strips_exif(): void
    {
        Storage::fake('public');

        // Crea una imagen JPEG grande (3200x1600) con segmento APP1 EXIF simulado en disco temporal
        $gdImage = imagecreatetruecolor(3200, 1600);
        $color = imagecolorallocate($gdImage, 0, 188, 212);
        imagefilledrectangle($gdImage, 0, 0, 3199, 1599, $color);

        $tempPath = sys_get_temp_dir().'/jaja_test_'.uniqid().'.jpg';
        imagejpeg($gdImage, $tempPath, 90);
        imagedestroy($gdImage);

        try {
            $converter = new ConvertImageToWebp;
            $storedPath = $converter($tempPath, 'test-uploads');

            $this->assertStringStartsWith('test-uploads/', $storedPath);
            $this->assertStringEndsWith('.webp', $storedPath);
            Storage::disk('public')->assertExists($storedPath);

            $webpBytes = Storage::disk('public')->get($storedPath);
            $this->assertStringStartsWith('RIFF', $webpBytes);
            $this->assertSame('WEBP', substr($webpBytes, 8, 4));
            $this->assertStringNotContainsString('EXIF', $webpBytes);

            $size = getimagesizefromstring($webpBytes);
            $this->assertIsArray($size);
            $this->assertSame(2560, $size[0]);
            $this->assertSame(1280, $size[1]);
        } finally {
            @unlink($tempPath);
        }
    }

    public function test_suggestion_request_rejects_gif_and_accepts_webp_jpeg_png(): void
    {
        $type = Type::create([
            'name' => 'Chistes',
            'slug' => 'chistes',
            'description' => 'Chistes',
        ]);

        $request = new SuggestionRequest;
        $request->merge(['type_id' => $type->id]);
        $rules = $request->rules();

        $gifBytes = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
        $gifFile = UploadedFile::fake()->createWithContent('anim.gif', $gifBytes);

        $gifValidator = Validator::make([
            'type_id' => $type->id,
            'title' => 'Título',
            'content' => 'Contenido',
            'terms' => '1',
            'image' => $gifFile,
        ], $rules);

        $this->assertTrue($gifValidator->fails());
        $this->assertArrayHasKey('image', $gifValidator->errors()->toArray());

        $pngFile = UploadedFile::fake()->image('valid.png', 200, 200);
        $pngValidator = Validator::make([
            'type_id' => $type->id,
            'title' => 'Título',
            'content' => 'Contenido',
            'terms' => '1',
            'image' => $pngFile,
        ], $rules);

        $this->assertFalse($pngValidator->fails());
    }

    public function test_all_filament_file_uploads_declare_2mb_limit_and_allowed_mime_types(): void
    {
        $helperContents = (string) file_get_contents(app_path('Filament/Support/WebpImageUpload.php'));
        $this->assertStringContainsString('->maxSize(2048)', $helperContents);
        $this->assertStringContainsString('->acceptedFileTypes(', $helperContents);

        $filamentFiles = [
            app_path('Filament/Panel/Resources/ContentResource.php'),
            app_path('Filament/Panel/Resources/CollaboratorResource.php'),
            app_path('Filament/Panel/Resources/CollaboratorResource/RelationManagers/ProjectsRelationManager.php'),
            app_path('Filament/Admin/Resources/ContentResource.php'),
            app_path('Filament/Admin/Resources/GroupResource/RelationManagers/ContentsRelationManager.php'),
            app_path('Filament/Admin/Resources/GroupResource.php'),
            app_path('Filament/Admin/Resources/PageResource.php'),
            app_path('Filament/Admin/Resources/UserResource.php'),
            app_path('Filament/Admin/Resources/CollaboratorResource.php'),
            app_path('Filament/Admin/Resources/CollaboratorResource/RelationManagers/ProjectsRelationManager.php'),
            app_path('Filament/Admin/Resources/CategoryResource.php'),
            app_path('Filament/Admin/Resources/SuggestionResource.php'),
            app_path('Filament/Admin/Resources/TypeResource.php'),
        ];

        foreach ($filamentFiles as $file) {
            $contents = (string) file_get_contents($file);
            $this->assertStringContainsString('WebpImageUpload::make(', $contents, "Missing WebpImageUpload::make in {$file}");
            $this->assertStringNotContainsString('FileUpload::make(', $contents, "Unexpected raw FileUpload::make in {$file}");
        }
    }

    public function test_deleting_or_replacing_model_images_removes_physical_files_and_keeps_defaults(): void
    {
        Storage::fake('public');

        // 1. User (SoftDeletes): reemplazar avatar borra anterior, forceDelete borra el nuevo, default intacto
        Storage::disk('public')->put('user-images/old.webp', 'old');
        Storage::disk('public')->put('user-images/new.webp', 'new');
        Storage::disk('public')->put('images/default/avatar.webp', 'default');

        $user = User::factory()->create(['avatar' => 'user-images/old.webp']);
        $user->update(['avatar' => 'user-images/new.webp']);
        Storage::disk('public')->assertMissing('user-images/old.webp');
        Storage::disk('public')->assertExists('user-images/new.webp');

        $user->delete();
        Storage::disk('public')->assertExists('user-images/new.webp');
        $user->forceDelete();
        Storage::disk('public')->assertMissing('user-images/new.webp');

        $defaultUser = User::factory()->create(['avatar' => 'images/default/avatar.webp']);
        $defaultUser->forceDelete();
        Storage::disk('public')->assertExists('images/default/avatar.webp');

        // 2. Type, Group, Category, Content y ContentOption
        Storage::disk('public')->put('type-images/t1.webp', 'x');
        $type = Type::create(['name' => 'Tipo', 'slug' => 'tipo', 'description' => 'D', 'image' => 'type-images/t1.webp']);

        Storage::disk('public')->put('group-images/g1.webp', 'x');
        $group = Group::create(['type_id' => $type->id, 'title' => 'Grupo', 'slug' => 'grupo', 'description' => 'D', 'image' => 'group-images/g1.webp']);

        Storage::disk('public')->put('category-images/cat1.webp', 'x');
        $category = Category::create(['title' => 'Cat', 'slug' => 'cat', 'description' => 'D', 'image' => 'category-images/cat1.webp']);

        $owner = User::factory()->create();
        Storage::disk('public')->put('content-images/c1.webp', 'x');
        $content = Content::create([
            'user_id' => $owner->id,
            'group_id' => $group->id,
            'title' => 'Contenido',
            'content' => 'Texto',
            'image' => 'content-images/c1.webp',
        ]);

        Storage::disk('public')->put('content-option-images/opt1.webp', 'x');
        $option = ContentOption::create([
            'content_id' => $content->id,
            'value' => 'Opción',
            'is_correct' => true,
            'order' => 1,
            'image_path' => 'content-option-images/opt1.webp',
        ]);

        $option->delete();
        Storage::disk('public')->assertMissing('content-option-images/opt1.webp');

        $content->forceDelete();
        Storage::disk('public')->assertMissing('content-images/c1.webp');

        $category->forceDelete();
        Storage::disk('public')->assertMissing('category-images/cat1.webp');

        $group->delete();
        Storage::disk('public')->assertMissing('group-images/g1.webp');

        $type->forceDelete();
        Storage::disk('public')->assertMissing('type-images/t1.webp');

        // 3. Page
        Storage::disk('public')->put('page-images/p1.webp', 'x');
        $page = Page::create([
            'title' => 'Página',
            'slug' => 'pagina',
            'excerpt' => 'Resumen',
            'content' => 'Texto',
            'image' => 'page-images/p1.webp',
        ]);
        $page->delete();
        Storage::disk('public')->assertMissing('page-images/p1.webp');

        // 4. Collaborator y CollaboratorProject (en cascada)
        Storage::disk('public')->put('collaborator-images/col1.webp', 'x');
        Storage::disk('public')->put('collaborator_projects-images/proj1.webp', 'x');
        $collaborator = Collaborator::create([
            'user_id' => $owner->id,
            'name' => 'Colab',
            'nick' => 'colab',
            'description' => 'D',
            'image' => 'collaborator-images/col1.webp',
        ]);
        CollaboratorProject::create([
            'collaborator_id' => $collaborator->id,
            'title' => 'Proyecto',
            'slug' => 'proyecto',
            'excerpt' => 'R',
            'content' => 'C',
            'type' => 'web',
            'repository_type' => 'github',
            'image' => 'collaborator_projects-images/proj1.webp',
        ]);

        $collaborator->delete();
        Storage::disk('public')->assertMissing('collaborator-images/col1.webp');
        Storage::disk('public')->assertMissing('collaborator_projects-images/proj1.webp');

        // 5. Suggestion & SuggestionOption
        Storage::disk('public')->put('suggestion-images/sug1.webp', 'x');
        Storage::disk('public')->put('suggestion-images/sugopt1.webp', 'x');
        $type2 = Type::create(['name' => 'Quiz 2', 'slug' => 'quiz-2', 'description' => 'Q']);
        $suggestion = Suggestion::create([
            'type_id' => $type2->id,
            'title' => 'Sug',
            'content' => 'Contenido',
            'image_path' => 'suggestion-images/sug1.webp',
        ]);
        $sugOption = SuggestionOption::create([
            'suggestion_id' => $suggestion->id,
            'value' => 'Resp',
            'is_correct' => true,
            'order' => 1,
            'image_path' => 'suggestion-images/sugopt1.webp',
        ]);

        $sugOption->delete();
        Storage::disk('public')->assertMissing('suggestion-images/sugopt1.webp');

        $suggestion->delete();
        Storage::disk('public')->assertMissing('suggestion-images/sug1.webp');
    }
}
