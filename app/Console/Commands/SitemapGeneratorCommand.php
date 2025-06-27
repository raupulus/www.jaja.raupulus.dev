<?php

namespace App\Console\Commands;

use App\Models\Collaborator;
use App\Models\Page;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use Carbon\Carbon;

class SitemapGeneratorCommand extends Command
{
    protected $signature = 'sitemap:generate
                            {--force : Forzar regeneración sin cache}
                            {--chunk=100 : Número de registros por chunk}';

    protected $description = 'Genera el sitemap del sitio completo con metadatos optimizados';

    private const CACHE_KEY = 'sitemap_generation_lock';
    private const CACHE_TTL = 3600; // 1 hora

    public function handle()
    {
        if (!$this->option('force') && Cache::has(self::CACHE_KEY)) {
            $this->warn('Generación de sitemap ya en progreso o reciente. Use --force para omitir.');
            return self::SUCCESS;
        }

        Cache::put(self::CACHE_KEY, true, self::CACHE_TTL);

        try {
            $this->info('🚀 Iniciando generación de sitemap...');

            $sitemap = $this->createBaseSitemap();
            $this->addPagesToSitemap($sitemap);
            $this->addCollaboratorsToSitemap($sitemap);

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

        ## URLs estáticas con prioridades y frecuencia de actualización
        $staticUrls = [
            ['url' => route('index'), 'priority' => 1.0, 'changefreq' => 'daily'],
            ['url' => route('page.index'), 'priority' => 0.8, 'changefreq' => 'weekly'],
            ['url' => url('docs'), 'priority' => 0.7, 'changefreq' => 'monthly'],
            ['url' => route('collaborator.index'), 'priority' => 0.9, 'changefreq' => 'daily'],
        ];

        foreach ($staticUrls as $urlData) {
            $sitemap->add(
                Url::create($urlData['url'])
                    ->setPriority($urlData['priority'])
                    ->setChangeFrequency($urlData['changefreq'])
                    ->setLastModificationDate(Carbon::now())
            );
        }

        return $sitemap;
    }

    private function addPagesToSitemap(Sitemap $sitemap): void
    {
        $this->info('📄 Agregando páginas al sitemap...');

        $chunkSize = $this->option('chunk');
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
            ## Agregar perfil del colaborador
            $sitemap->add(
                Url::create(route('collaborator.show', $collaborator->nick))
                    ->setPriority(0.8)
                    ->setChangeFrequency('weekly')
                    ->setLastModificationDate($collaborator->updated_at ?? $collaborator->created_at)
            );
            $collaboratorCount++;

            ## Agrego proyectos del colaborador
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

        $sitemapPath = public_path('sitemap.xml');
        $backupPath = public_path('sitemap_backup.xml');

        ## Creo backup del sitemap anterior si existe
        if (file_exists($sitemapPath)) {
            copy($sitemapPath, $backupPath);
        }

        try {
            $sitemap->writeToFile($sitemapPath);

            ## Verifico que el archivo se escribió correctamente
            if (!file_exists($sitemapPath) || filesize($sitemapPath) === 0) {
                throw new \Exception('El archivo sitemap.xml está vacío o no se pudo crear');
            }

            ## Elimino backup si todo salió bien
            if (file_exists($backupPath)) {
                unlink($backupPath);
            }

            $this->info('   ✓ Sitemap escrito correctamente');

        } catch (\Exception $e) {
            ## Restaura backup si algo sale mal
            if (file_exists($backupPath)) {
                copy($backupPath, $sitemapPath);
                unlink($backupPath);
            }
            throw $e;
        }
    }

    private function handleError(\Exception $e): void
    {
        $this->error('❌ Error al generar el sitemap: ' . $e->getMessage());

        Log::error('SitemapGeneratorCommand: Error al generar el sitemap', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true),
        ]);
    }
}
