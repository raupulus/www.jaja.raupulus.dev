<?php

namespace App\Http\Controllers;

use App\Actions\ConvertImageToWebp;
use App\Helpers\StatsHelper;
use App\Http\Requests\SuggestionRequest;
use App\Models\Content;
use App\Models\Suggestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Controlador IndexController
 *
 * Gestiona la página principal (Home) de la web, resúmenes, estadísticas públicas y redirecciones básicas.
 */
class IndexController extends Controller
{
    /**
     * Página principal.
     */
    public function index(): View
    {
        $contents = Cache::remember('home_contents', 30, function () {
            return Content::with([
                'group.type',
                'options' => fn ($q) => $q->orderBy('order'),
                'user:id,nick',
            ])
                ->publicSafe()
                ->inRandomOrder()
                ->take(10)
                ->get()
                ->sortByDesc('image');
        });

        $counts = StatsHelper::typesAndGroupsAndCategoriesCount();

        return view('home')->with([
            'contents' => $contents,
            'typesCount' => $counts['typesCount'],
            'groupsCount' => $counts['groupsCount'],
            'categoriesCount' => $counts['categoriesCount'],
        ]);
    }

    /**
     * Procesa el envío de una sugerencia de contenido.
     */
    public function sendSuggestion(SuggestionRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        unset($validated['image'], $validated['terms']);

        $options = $validated['options'] ?? [];
        unset($validated['options']);

        $webpPath = null;

        if ($request->hasFile('image')) {
            $tempPath = $request->file('image')?->store('suggestion-images', 'public');

            if ($tempPath) {
                $fullTempPath = Storage::disk('public')->path($tempPath);

                try {
                    $convertToWebp = new ConvertImageToWebp;
                    $webpPath = $convertToWebp($fullTempPath, 'suggestion-images');
                } finally {
                    Storage::disk('public')->delete($tempPath);
                }
            }
        }

        if ($webpPath) {
            $validated['image_path'] = $webpPath;
        }

        try {
            DB::transaction(function () use ($validated, $options): void {
                $suggestion = Suggestion::create($validated);

                if (! empty($options)) {
                    $suggestion->options()->createMany($options);
                }
            });
        } catch (\Throwable $e) {
            if ($webpPath && Storage::disk('public')->exists($webpPath)) {
                Storage::disk('public')->delete($webpPath);
            }

            throw $e;
        }

        return redirect()->route('index')->with('success', 'Contenido enviado correctamente.');
    }

    /**
     * Login con redirección al panel para usuarios
     */
    public function login(): RedirectResponse
    {
        return redirect()->route('filament.panel.auth.login');
    }
}
