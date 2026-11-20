<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Collaborator;
use App\Models\CollaboratorProject;
use App\Models\Content;
use App\Models\Group;
use App\Models\Page;
use App\Models\Type;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapGeneratorCommand extends Command
{
    protected $signature = 'sitemap:generate
                            {--force : Forzar regeneración sin cache}
                            {--chunk=100 : Número de registros por chunk}
                            {--output= : Ruta alternativa del archivo sitemap}';

    protected $description = 'Genera el sitemap del sitio completo con metadatos optimizados';

    private const CACHE_KEY = 'sitemap_generation_lock';

    private const CACHE_TTL = 3600; // 1 hora

    public function handle()
    {
        if (! $this->option('force') && Cache::has(self::CACHE_KEY)) {
            $this->warn('Generación de sitemap ya en progreso o reciente. Use --force para omitir.');

            return self::SUCCESS;
        }

        Cache::put(self::CACHE_KEY, true, self::CACHE_TTL);

        try {
            $this->info('🚀 Iniciando generación de sitemap...');

            $sitemap = $this->createBaseSitemap();
            $this->addPagesToSitemap($sitemap);
            $this->addCollaboratorsToSitemap($sitemap);
            $this->addContentToSitemap($sitemap);

            $this->writeSitemapToFile($sitemap);

            $this->info('✅ Sitemap generado correctamente');

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->handleError($e);

            return self::FAILURE;
        } finally {
            Cache::forget(self::CACHE_KEY);
        }
    }

    private function createBaseSitemap(): Sitemap
    {
        $sitemap = Sitemap::create();

        $latestContentAt = Content::publicSafe()
            ->max('updated_at');
        $contentModAt = $latestContentAt ? Carbon::parse($latestContentAt) : Carbon::now();

        $latestPageAt = Page::where('status', 'published')->max('updated_at');
        $pageModAt = $latestPageAt ? Carbon::parse($latestPageAt) : Carbon::now();

        $latestCollabAt = CollaboratorProject::where('status', 'published')->max('updated_at');
        $collabModAt = $latestCollabAt ? Carbon::parse($latestCollabAt) : Carbon::now();

        $docsFile = file_exists(public_path('docs/index.html'))
            ? public_path('docs/index.html')
            : resource_path('views/scribe/index.blade.php');
        $docsModAt = file_exists($docsFile)
            ? Carbon::createFromTimestamp(filemtime($docsFile))
            : Carbon::now();

        // # URLs estáticas con prioridades, frecuencia y fecha real de modificación
        $staticUrls = [
            ['url' => route('index'), 'priority' => 1.0, 'changefreq' => 'daily', 'lastmod' => $contentModAt],
            ['url' => route('page.index'), 'priority' => 0.8, 'changefreq' => 'weekly', 'lastmod' => $pageModAt],
            ['url' => route('collaborator.index'), 'priority' => 0.9, 'changefreq' => 'weekly', 'lastmod' => $collabModAt],
            ['url' => route('content.types.index'), 'priority' => 0.7, 'changefreq' => 'monthly', 'lastmod' => $contentModAt],
            ['url' => route('content.categories.index'), 'priority' => 0.8, 'changefreq' => 'monthly', 'lastmod' => $contentModAt],
            ['url' => route('content.groups.index'), 'priority' => 0.8, 'changefreq' => 'weekly', 'lastmod' => $contentModAt],
            ['url' => url('docs'), 'priority' => 0.7, 'changefreq' => 'monthly', 'lastmod' => $docsModAt],
        ];

        foreach ($staticUrls as $urlData) {
            $sitemap->add(
                Url::create($urlData['url'])
                    ->setPriority($urlData['priority'])
                    ->setChangeFrequency($urlData['changefreq'])
                    ->setLastModificationDate($urlData['lastmod'])
            );
        }

        foreach (Type::all() as $type) {
            $latestTypeContent = $type->contents()
                ->publicSafe()
                ->max('contents.updated_at');

            $typeModAt = $latestTypeContent
                ? Carbon::parse($latestTypeContent)
                : ($type->updated_at ?? $type->created_at ?? Carbon::now());

            $sitemap->add(
                Url::create(route('content.type.group.index', $type->slug))
                    ->setPriority(0.8)
                    ->setChangeFrequency('weekly')
                    ->setLastModificationDate($typeModAt)
            );
        }

        return $sitemap;
    }

    private function addContentToSitemap(Sitemap $sitemap): void
    {
        $this->info('Agregando páginas de grupos al sitemap...');

        $chunkSize = max(1, (int) $this->option('chunk'));
        $groupCount = 0;

        Group::publicSafe()
            ->hasAvailableContent()
            ->chunk($chunkSize, function ($groups) use ($sitemap, &$groupCount) {
                foreach ($groups as $group) {

                    $lastModContent = $group->contents()
                        ->publicSafe()
                        ->orderBy('updated_at', 'desc')
                        ->first();
                    $lastModContent = $lastModContent ? $lastModContent->updated_at : $group->created_at;

                    $sitemap->add(
                        Url::create(route('content.group.content.random', $group->slug))
                            ->setPriority(0.7)
                            ->setChangeFrequency('weekly')
                            ->setLastModificationDate($lastModContent)
                    );

                    $groupCount++;
                }
            });

        $this->info("   ✓ {$groupCount} enlaces a páginas de grupos creados");

        $categoryCount = 0;

        Category::hasAvailableContent()
            ->chunk($chunkSize, function ($categories) use ($sitemap, &$categoryCount) {
                foreach ($categories as $category) {

                    $lastModContent = $category->contents()
                        ->publicSafe()
                        ->orderBy('updated_at', 'desc')
                        ->first();
                    $lastModContent = $lastModContent ? $lastModContent->updated_at : $category->created_at;

                    $sitemap->add(
                        Url::create(route('content.categoria.content.random', $category->slug))
                            ->setPriority(0.7)
                            ->setChangeFrequency('weekly')
                            ->setLastModificationDate($lastModContent)
                    );

                    $categoryCount++;
                }
            });

        $this->info("   ✓ {$categoryCount} enlaces a páginas de categorías creados");

    }

    private function addPagesToSitemap(Sitemap $sitemap): void
    {
        $this->info('📄 Agregando páginas al sitemap...');

        $chunkSize = max(1, (int) $this->option('chunk'));
        $pageCount = 0;

        Page::where('status', 'published')
            ->chunk($chunkSize, function ($pages) use ($sitemap, &$pageCount) {
                foreach ($pages as $page) {
                    $sitemap->add(
                        Url::create(route('page.show', $page->slug))
                            ->setPriority(0.7)
                            ->setChangeFrequency('weekly')
                            ->setLastModificationDate($page->updated_at ?? $page->created_at)
                    );
                    $pageCount++;
                }
            });

        $this->info("   ✓ {$pageCount} páginas agregadas");
    }

    private function addCollaboratorsToSitemap(Sitemap $sitemap): void
    {
        $this->info('👥 Agregando colaboradores y proyectos...');

        $collaboratorCount = 0;
        $projectCount = 0;

        $collaborators = Collaborator::getCollaboratorsVerified();

        foreach ($collaborators as $collaborator) {
            // # Agregar perfil del colaborador
            $sitemap->add(
                Url::create(route('collaborator.show', $collaborator->nick))
                    ->setPriority(0.8)
                    ->setChangeFrequency('weekly')
                    ->setLastModificationDate($collaborator->updated_at ?? $collaborator->created_at)
            );
            $collaboratorCount++;

            // # Agrego proyectos del colaborador
            $projects = $collaborator->projects()
                ->where('status', 'published')
                ->get();

            foreach ($projects as $project) {
                $sitemap->add(
                    Url::create(route('collaborator.project.show', [$collaborator->nick, $project->slug]))
                        ->setPriority(0.6)
                        ->setChangeFrequency('monthly')
                        ->setLastModificationDate($project->updated_at ?? $project->created_at)
                );
                $projectCount++;
            }
        }

        $this->info("   ✓ {$collaboratorCount} colaboradores agregados");
        $this->info("   ✓ {$projectCount} proyectos agregados");
    }

    private function writeSitemapToFile(Sitemap $sitemap): void
    {
        $this->info('💾 Escribiendo sitemap a archivo...');

        $sitemapPath = $this->option('output') ?: public_path('sitemap.xml');
        $backupPath = $sitemapPath.'.bak';

        // Creo backup del sitemap anterior si existe
        if (file_exists($sitemapPath)) {
            try {
                copy($sitemapPath, $backupPath);
                $this->info('   ✓ Backup del sitemap anterior creado');
            } catch (\Exception $e) {
                $this->warn('   ⚠️ No se pudo crear el backup: '.$e->getMessage());
                Log::warning('SitemapGeneratorCommand: Failed to create sitemap backup', [
                    'error' => $e->getMessage(),
                    'sitemap_path' => $sitemapPath,
                    'backup_path' => $backupPath,
                ]);
            }
        }

        try {
            // # Si el sitemap existe, vacío su contenido en lugar de eliminarlo
            if (file_exists($sitemapPath)) {
                if (! is_writable($sitemapPath)) {
                    throw new \Exception('El archivo sitemap.xml no tiene permisos de escritura');
                }

                // # Vacío el contenido del archivo existente
                file_put_contents($sitemapPath, '');
                $this->info('   ✓ Contenido del sitemap anterior vaciado');
            }

            $sitemap->writeToFile($sitemapPath);

            // # Verifico que el archivo se escribió correctamente
            if (! file_exists($sitemapPath) || filesize($sitemapPath) === 0) {
                throw new \Exception('El archivo sitemap.xml está vacío o no se pudo crear');
            }

            // # Elimino backup si todo salió bien
            if (file_exists($backupPath)) {
                try {
                    unlink($backupPath);
                    $this->info('   ✓ Backup temporal eliminado');
                } catch (\Exception $e) {
                    $this->warn('   ⚠️ No se pudo eliminar el backup temporal: '.$e->getMessage());
                    Log::warning('SitemapGeneratorCommand: Failed to remove temporary sitemap backup', [
                        'error' => $e->getMessage(),
                        'backup_path' => $backupPath,
                    ]);
                }
            }

            $this->info('   ✓ Sitemap escrito correctamente');

        } catch (\Exception $e) {
            // # Restauro backup si algo sale mal y si existe
            if (file_exists($backupPath)) {
                try {
                    copy($backupPath, $sitemapPath);
                    $this->info('   ✓ Sitemap restaurado desde backup');

                    // # Intento eliminar el backup después de restaurar
                    try {
                        unlink($backupPath);
                    } catch (\Exception $unlinkError) {
                        $this->warn('   ⚠️ No se pudo eliminar el backup después de restaurar: '.$unlinkError->getMessage());
                    }
                } catch (\Exception $restoreError) {
                    $this->error('   ❌ No se pudo restaurar el backup: '.$restoreError->getMessage());
                    Log::error('SitemapGeneratorCommand: Failed to restore sitemap backup', [
                        'restore_error' => $restoreError->getMessage(),
                        'original_error' => $e->getMessage(),
                        'backup_path' => $backupPath,
                        'sitemap_path' => $sitemapPath,
                    ]);
                }
            }

            throw $e;
        }
    }

    private function handleError(\Exception $e): void
    {
        $this->error('❌ Error al generar el sitemap: '.$e->getMessage());

        Log::error('SitemapGeneratorCommand: Failed to generate sitemap', [
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true),
        ]);
    }
}
