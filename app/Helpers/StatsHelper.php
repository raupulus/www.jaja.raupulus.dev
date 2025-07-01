<?php

namespace App\Helpers;

use App\Models\Content;
use App\Models\Suggestion;
use App\Models\User;

class StatsHelper
{
    /**
     * Sugerencias de contenido pendientes de aceptación.
     *
     * @return int
     */
    public static function getSuggestionsPending():int
    {
        return cache()->remember('suggestions_pending_count', 60*60, function () {
            return Suggestion::whereNull('approved_at')->count();
        });
    }

    /**
     * Sugerencias de contenido aceptadas.
     *
     * @return int
     */
    public static function getSuggestionsAccepted(): int
    {
        return cache()->remember('suggestions_accepted_count', 60*60, function () {
            return Suggestion::whereNotNull('approved_at')->count();
        });
    }

    /**
     * Contenidos activos en la plataforma.
     *
     * @return int
     */
    public static function getContentsTotal(): int
    {
        return cache()->remember('contents_count', 60*60, function () {
            return Content::count();
        });
    }

    /**
     * Suma del contenido activo y las sugerencias de contenido pendientes.
     *
     * @return int
     */
    public static function getContentsAndSuggestionsTotal(): int
    {
        return self::getContentsTotal() + self::getSuggestionsPending();
    }

    /**
     * Cantidad total de usuarios activos en la plataforma.
     *
     * @return int
     */
    public static function getUsersActiveTotal(): int
    {
        return cache()->remember('users_active_total', 60*60, function () {
            $nicks = Content::distinct('uploaded_by')->count();
            $users = User::distinct('nick')->count();

            return $nicks + $users;
        });
    }

    /**
     * Usuarios que más contribuciones han enviado a la plataforma.
     *
     * @param int $limit Cantidad máxima de resultados.
     * @return array[]
     */
    public static function getUsersMoreActive(int $limit = 20): array
    {
        return cache()->remember("users_more_active_{$limit}", 60*60, function () use ($limit) {
            ## Usuarios anónimos más activos limitados por $limit (tienen uploaded_by)
            $usuariosAnonimos = \DB::table('contents as c')
                ->join('groups as g', 'c.group_id', '=', 'g.id')
                ->join('types as t', 'g.type_id', '=', 't.id')
                ->whereNull('c.deleted_at')
                ->whereNotNull('c.uploaded_by')
                ->whereIn('t.slug', ['chistes', 'quiz', 'adivinanzas'])
                ->select([
                    'c.uploaded_by as nick',
                    \DB::raw("SUM(CASE WHEN t.slug = 'chistes' THEN 1 ELSE 0 END) as chistes"),
                    \DB::raw("SUM(CASE WHEN t.slug = 'quiz' THEN 1 ELSE 0 END) as quiz"),
                    \DB::raw("SUM(CASE WHEN t.slug = 'adivinanzas' THEN 1 ELSE 0 END) as adivinanzas"),
                    \DB::raw("COUNT(*) as total")
                ])
                ->groupBy('c.uploaded_by')
                ->orderBy('total', 'desc')
                ->limit($limit)
                ->get();

            ## Usuarios registrados limitados por $limit según contenidos con uploaded_by a null
            $usuariosRegistrados = \DB::table('contents as c')
                ->join('groups as g', 'c.group_id', '=', 'g.id')
                ->join('types as t', 'g.type_id', '=', 't.id')
                ->join('users as u', 'c.user_id', '=', 'u.id')
                ->whereNull('c.deleted_at')
                ->whereNull('c.uploaded_by')
                ->whereNotNull('c.user_id')
                ->whereIn('t.slug', ['chistes', 'quiz', 'adivinanzas'])
                ->select([
                    'u.nick as nick',
                    \DB::raw("SUM(CASE WHEN t.slug = 'chistes' THEN 1 ELSE 0 END) as chistes"),
                    \DB::raw("SUM(CASE WHEN t.slug = 'quiz' THEN 1 ELSE 0 END) as quiz"),
                    \DB::raw("SUM(CASE WHEN t.slug = 'adivinanzas' THEN 1 ELSE 0 END) as adivinanzas"),
                    \DB::raw("COUNT(*) as total")
                ])
                ->groupBy('u.id', 'u.nick')
                ->orderBy('total', 'desc')
                ->limit($limit)
                ->get();

            ## Combino, ordeno y preparo respuesta/resultados
            $todosLosUsuarios = collect($usuariosAnonimos)
                ->concat($usuariosRegistrados)
                ->map(function($row) {
                    return [
                        'nick' => $row->nick,
                        'chistes' => (int)$row->chistes,
                        'quiz' => (int)$row->quiz,
                        'adivinanzas' => (int)$row->adivinanzas,
                        'total' => (int)$row->total,
                    ];
                })
                ->sortByDesc('total')
                ->take($limit)
                ->values()
                ->toArray();

            return $todosLosUsuarios;
        });
    }
}
