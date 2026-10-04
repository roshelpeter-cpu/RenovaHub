<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Completed projects need a category breakdown that sums to estimated_budget.
     * Stage dates live on project_progress so the overview timeline is not Blade-hardcoded.
     */
    public function up(): void
    {
        Schema::create('project_budget_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('category');
            $table->decimal('amount', 14, 2);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['project_id', 'sort_order']);
        });

        Schema::table('project_progress', function (Blueprint $table) {
            $table->date('started_on')->nullable()->after('percent');
            $table->date('ended_on')->nullable()->after('started_on');
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->string('category', 80)->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn('category');
        });

        Schema::table('project_progress', function (Blueprint $table) {
            $table->dropColumn(['started_on', 'ended_on']);
        });

        Schema::dropIfExists('project_budget_items');
    }
};
