<?php

namespace App\Http\Controllers;

use App\Actions\ConvertImageToWebp;
use App\Http\Requests\SuggestionRequest;
use App\Models\Content;
use App\Models\Suggestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        return view('home')->with([
            'contents' => Content::inRandomOrder()->take(10)->get()->sortByDesc('image')
        ]);
    }

    public function api(): View
    {
        return view('api');
    }

    public function about(): View
    {
        return view('about');
    }

    public function normas(): View
    {
        return view('normas');
    }

    public function privacity(): View
    {
        return view('privacity');
    }

    public function cookies(): View
    {
        return view('cookies');
    }

    public function conditions(): View
    {
        return view('conditions');
    }

    public function agradecimientos(): View
    {
        return view('agradecimientos');
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
}
