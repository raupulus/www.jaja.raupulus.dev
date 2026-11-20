<?php

namespace Tests;

use App\Models\Category;
use Database\Factories\CategoryFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    protected function setUpTraits()
    {
        $uses = parent::setUpTraits();

        if (isset($uses[RefreshDatabase::class]) && DB::getDriverName() === 'pgsql') {
            $lastGroupSeq = (int) DB::scalar('SELECT last_value FROM groups_id_seq');

            if ($lastGroupSeq < 100) {
                DB::statement("SELECT setval(pg_get_serial_sequence('groups', 'id'), 100, false)");
            }
        }

        return $uses;
    }

    /**
     * Garantiza la existencia de la categoría General (ID 1) requerida por el boot de Content y Suggestion.
     */
    protected function ensureGeneralCategory(): Category
    {
        return CategoryFactory::ensureGeneralCategoryExists();
    }

    /**
     * Sincroniza la secuencia autoincremental en PostgreSQL tras inserciones con ID explícito.
     */
    protected function syncPostgresSequence(string $table, string $column = 'id', int $minValue = 1): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement(
                "SELECT setval(pg_get_serial_sequence('{$table}', '{$column}'), GREATEST({$minValue}, COALESCE((SELECT MAX({$column}) FROM {$table}), 1)), true)"
            );
        }
    }
}
