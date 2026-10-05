<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Designer work is stored beside the homeowner project, not in a second app.
 * Mood-board status is the approval gate: a draft is never treated as final.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mood_boards', function (Blueprint $table) {
            $table->string('status', 32)->default('draft')->after('version');
            $table->text('revision_note')->nullable()->after('status');
            $table->timestamp('submitted_at')->nullable()->after('revision_note');
            $table->index('status');
        });

        DB::table('mood_boards')->whereNotNull('approved_at')->update(['status' => 'approved']);

        Schema::create('design_concepts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('designer_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 32)->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'status']);
        });

        Schema::create('design_concept_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('design_concept_id')->constrained()->cascadeOnDelete();
            $table->string('kind', 32)->default('render');
            $table->string('path');
            $table->string('caption')->nullable();
            $table->timestamps();
        });

        Schema::create('design_change_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->string('design_impact')->nullable();
            $table->string('status', 32)->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'status']);
        });

        Schema::create('design_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('designer_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('category', 32)->default('design');
            $table->text('notes')->nullable();
            $table->string('status', 32)->default('pending');
            $table->unsignedTinyInteger('progress')->default(0);
            $table->date('due_on')->nullable();
            $table->string('attachment_path')->nullable();
            $table->timestamps();

            $table->index(['designer_id', 'status']);
        });

        Schema::create('designer_earnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('designer_id')->constrained('users')->cascadeOnDelete();
            $table->string('label');
            $table->decimal('gross_amount', 14, 2);
            $table->decimal('fee_percent', 5, 2);
            $table->decimal('fee_amount', 14, 2);
            $table->decimal('net_amount', 14, 2);
            $table->string('status', 32)->default('pending');
            $table->string('reference')->unique();
            $table->date('recorded_on')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['designer_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('designer_earnings');
        Schema::dropIfExists('design_tasks');
        Schema::dropIfExists('design_change_requests');
        Schema::dropIfExists('design_concept_files');
        Schema::dropIfExists('design_concepts');

        Schema::table('mood_boards', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropColumn(['status', 'revision_note', 'submitted_at']);
        });
    }
};
