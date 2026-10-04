<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Global task and document tables need dates, descriptions and spend
     * figures that already belong on the child rows, not in Blade.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->decimal('final_cost', 12, 2)->nullable()->after('current_budget');
        });

        Schema::table('project_tasks', function (Blueprint $table) {
            $table->date('started_on')->nullable()->after('priority');
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
        });

        Schema::table('project_budget_items', function (Blueprint $table) {
            $table->unsignedTinyInteger('spent_percent')->default(0)->after('amount');
        });

        Schema::table('project_progress', function (Blueprint $table) {
            $table->string('notes')->nullable()->after('ended_on');
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->timestamp('approved_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn('approved_at');
        });

        Schema::table('project_progress', function (Blueprint $table) {
            $table->dropColumn('notes');
        });

        Schema::table('project_budget_items', function (Blueprint $table) {
            $table->dropColumn('spent_percent');
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        Schema::table('project_tasks', function (Blueprint $table) {
            $table->dropColumn('started_on');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('final_cost');
        });
    }
};
