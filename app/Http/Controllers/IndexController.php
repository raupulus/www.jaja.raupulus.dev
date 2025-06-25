<?php

namespace App\Http\Controllers;

use App\Actions\ConvertImageToWebp;
use App\Http\Requests\SuggestionRequest;
use App\Models\Content;
use App\Models\Suggestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class IndexController extends Controller
{
    /**
     * Página principal.
     *
     * @return View
     */
    public function index(): View
    {
        $contents = Cache::remember('home_contents', 60, function () {
            return Content::inRandomOrder()->take(10)->get()->sortByDesc('image');
        });

        return view('home')->with([
            'contents' => $contents
        ]);
    }

    /**
     * Procesa el envío de una sugerencia de contenido.
     *
     * @param SuggestionRequest $request
     * @return RedirectResponse
     */
    public function sendSuggestion(SuggestionRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        unset($validated['image']);

        $suggestion = Suggestion::create($validated);

        if ($request->hasFile('image')) {
            $tempPath = $request->file('image')?->store('suggestion-images', 'public');

            if ($tempPath) {
                $fullTempPath = Storage::disk('public')->path($tempPath);

                $convertToWebp = new ConvertImageToWebp();
                $webpPath = $convertToWebp($fullTempPath, 'suggestion-images');

                $suggestion->update([
                    'image_path' => $webpPath,
                ]);

                Storage::disk('public')->delete($tempPath);
            }
        }

        return redirect()->route('index')->with('success', 'Contenido enviado correctamente.');
    }

    /**
     * Login con redirección al panel para usuarios
     *
     * @return RedirectResponse
     */
    public function login(): RedirectResponse
    {
        return redirect()->route('filament.panel.auth.login');
    }
}
