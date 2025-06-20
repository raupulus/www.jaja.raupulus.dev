<?php

namespace App\Console\Commands;

use App\Models\Collaborator;
use App\Models\Page;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Spatie\Sitemap\Sitemap;

class SitemapGeneratorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera el sitemap del sitio completo';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generando sitemap');

        try {
            $sitemap = Sitemap::create()
                ->add(route('index'))
                ->add(route('page.index'))
                ->add(route('collaborator.index'));

            $pages = Page::where('status', 'published')->get();

            foreach ($pages as $page) {
                $sitemap->add(route('page.show', $page->slug));
            }

            $collaborators = Collaborator::getCollaboratorsVerified();

            foreach ($collaborators as $collaborator) {
                $sitemap->add(route('collaborator.show', $collaborator->nick));

                $projects = $collaborator->projects()->where('status', 'published')->get();

                foreach ($projects as $project) {
                    $sitemap->add(route('collaborator.project.show', [$collaborator->nick, $project->slug]));
                }
            }

            $sitemap->writeToFile(public_path('sitemap.xml'));

            $this->info('✓ Sitemap Generado correctamente');

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error al generar el sitemap: ' . $e->getMessage());

            Log::error('SitemapGeneratorCommand: Error al generar el sitemap', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return self::FAILURE;
        }
    }
}
