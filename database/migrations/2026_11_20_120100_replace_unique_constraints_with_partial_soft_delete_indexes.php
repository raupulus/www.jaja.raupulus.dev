<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tablas con SoftDeletes cuyas restricciones únicas se convierten en parciales.
     *
     * @return list<array{table: string, column: string, old: string, partial: string}>
     */
    private function targets(): array
    {
        return [
            ['table' => 'users', 'column' => 'email', 'old' => 'users_email_unique', 'partial' => 'users_email_unique_active'],
            ['table' => 'categories', 'column' => 'slug', 'old' => 'categories_slug_unique', 'partial' => 'categories_slug_unique_active'],
            ['table' => 'types', 'column' => 'slug', 'old' => 'types_slug_unique', 'partial' => 'types_slug_unique_active'],
            ['table' => 'groups', 'column' => 'slug', 'old' => 'groups_slug_unique', 'partial' => 'groups_slug_unique_active'],
            ['table' => 'pages', 'column' => 'slug', 'old' => 'pages_slug_unique', 'partial' => 'pages_slug_unique_active'],
        ];
    }

    /**
     * Sustituye las restricciones únicas estándar por índices únicos parciales WHERE deleted_at IS NULL.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if (! in_array($driver, ['pgsql', 'sqlite'], true)) {
            return;
        }

        foreach ($this->targets() as $target) {
            Schema::table($target['table'], function (Blueprint $table) use ($target): void {
                $table->dropUnique($target['old']);
            });

            DB::statement(sprintf(
                'CREATE UNIQUE INDEX %s ON %s (%s) WHERE deleted_at IS NULL',
                $target['partial'],
                $target['table'],
                $target['column'],
            ));
        }
    }

    /**
     * Restaura las restricciones únicas estándar eliminando los índices parciales.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if (! in_array($driver, ['pgsql', 'sqlite'], true)) {
            return;
        }

        foreach (array_reverse($this->targets()) as $target) {
            DB::statement(sprintf('DROP INDEX IF EXISTS %s', $target['partial']));

            Schema::table($target['table'], function (Blueprint $table) use ($target): void {
                $table->unique($target['column'], $target['old']);
            });
        }
    }
};
