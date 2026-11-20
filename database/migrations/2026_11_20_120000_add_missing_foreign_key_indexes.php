<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Añade índices en claves foráneas y columnas frecuentes sin alterar datos existentes.
     */
    public function up(): void
    {
        Schema::table('content_categories', function (Blueprint $table): void {
            $table->index(['content_id', 'category_id'], 'content_categories_content_id_category_id_index');
            $table->index('category_id', 'content_categories_category_id_index');
        });

        Schema::table('content_options', function (Blueprint $table): void {
            $table->index(['content_id', 'order'], 'content_options_content_id_order_index');
        });

        Schema::table('suggestions', function (Blueprint $table): void {
            $table->index('type_id', 'suggestions_type_id_index');
            $table->index('group_id', 'suggestions_group_id_index');
            $table->index('approved_at', 'suggestions_approved_at_index');
        });

        Schema::table('suggestion_options', function (Blueprint $table): void {
            $table->index(['suggestion_id', 'order'], 'suggestion_options_suggestion_id_order_index');
        });

        Schema::table('suggestion_categories', function (Blueprint $table): void {
            $table->index(['suggestion_id', 'category_id'], 'suggestion_categories_suggestion_id_category_id_index');
            $table->index('category_id', 'suggestion_categories_category_id_index');
        });

        Schema::table('collaborators', function (Blueprint $table): void {
            $table->index('user_id', 'collaborators_user_id_index');
        });

        Schema::table('collaborator_projects', function (Blueprint $table): void {
            $table->index(['collaborator_id', 'status'], 'collaborator_projects_collaborator_id_status_index');
        });

        Schema::table('reports', function (Blueprint $table): void {
            $table->index('user_id', 'reports_user_id_index');
            $table->index('assigned_to', 'reports_assigned_to_index');
        });

        Schema::table('contents', function (Blueprint $table): void {
            $table->index(['is_adult', 'group_id'], 'contents_is_adult_group_id_index');
        });
    }

    /**
     * Revierte los índices añadidos de forma segura.
     */
    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table): void {
            $table->dropIndex('contents_is_adult_group_id_index');
        });

        Schema::table('reports', function (Blueprint $table): void {
            $table->dropIndex('reports_user_id_index');
            $table->dropIndex('reports_assigned_to_index');
        });

        Schema::table('collaborator_projects', function (Blueprint $table): void {
            $table->dropIndex('collaborator_projects_collaborator_id_status_index');
        });

        Schema::table('collaborators', function (Blueprint $table): void {
            $table->dropIndex('collaborators_user_id_index');
        });

        Schema::table('suggestion_categories', function (Blueprint $table): void {
            $table->dropIndex('suggestion_categories_suggestion_id_category_id_index');
            $table->dropIndex('suggestion_categories_category_id_index');
        });

        Schema::table('suggestion_options', function (Blueprint $table): void {
            $table->dropIndex('suggestion_options_suggestion_id_order_index');
        });

        Schema::table('suggestions', function (Blueprint $table): void {
            $table->dropIndex('suggestions_type_id_index');
            $table->dropIndex('suggestions_group_id_index');
            $table->dropIndex('suggestions_approved_at_index');
        });

        Schema::table('content_options', function (Blueprint $table): void {
            $table->dropIndex('content_options_content_id_order_index');
        });

        Schema::table('content_categories', function (Blueprint $table): void {
            $table->dropIndex('content_categories_content_id_category_id_index');
            $table->dropIndex('content_categories_category_id_index');
        });
    }
};
