<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The homeowner workspace hangs off the existing projects table.
     * Child records cascade with the project so deletion cannot leave orphans.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 30)->nullable()->after('email');
            $table->string('address')->nullable()->after('phone');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->text('requirements')->nullable()->after('description');
            $table->text('additional_instructions')->nullable()->after('requirements');
            $table->text('timeline_notes')->nullable()->after('expected_completion_date');
            $table->decimal('current_budget', 12, 2)->nullable()->after('estimated_budget');
            $table->date('actual_completion_date')->nullable()->after('expected_completion_date');
            $table->string('currency', 3)->default('LKR')->after('current_budget');
        });

        Schema::table('professional_profiles', function (Blueprint $table) {
            $table->string('title')->nullable()->after('business_name');
            $table->string('specialization')->nullable()->after('title');
            $table->string('avatar_path')->nullable()->after('location');
            $table->unsignedInteger('completed_projects_count')->default(0)->after('years_experience');
            $table->decimal('starting_price', 12, 2)->nullable()->after('rating');
        });

        Schema::table('portfolio_items', function (Blueprint $table) {
            $table->string('location')->nullable()->after('category');
            $table->decimal('budget_min', 12, 2)->nullable()->after('location');
            $table->decimal('budget_max', 12, 2)->nullable()->after('budget_min');
        });

        Schema::create('portfolio_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_item_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('caption')->nullable();
            $table->timestamps();
        });

        Schema::create('professional_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_profile_id')->constrained()->cascadeOnDelete();
            $table->string('author_name');
            $table->string('project_title')->nullable();
            $table->text('body');
            $table->decimal('rating', 2, 1);
            $table->timestamps();
        });

        Schema::create('project_reference_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('original_name');
            $table->timestamps();
        });

        // One row per invite. Accepted rows are the confirmed team.
        // A declined invite stays so the homeowner can choose someone else.
        Schema::create('project_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role', 20);
            $table->string('status', 20)->default('pending');
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'role', 'status']);
        });

        Schema::create('project_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('stage', 30);
            $table->unsignedTinyInteger('percent')->default(0);
            $table->timestamps();

            $table->unique(['project_id', 'stage']);
        });

        Schema::create('milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('due_on')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'due_on']);
        });

        Schema::create('project_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category', 30);
            $table->string('status', 20)->default('pending');
            $table->string('priority', 20)->default('normal');
            $table->date('due_on')->nullable();
            $table->unsignedTinyInteger('progress')->default(0);
            $table->timestamps();

            $table->index(['project_id', 'status']);
        });

        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('original_name');
            $table->string('category', 30);
            $table->string('disk', 20)->default('public');
            $table->string('path');
            $table->string('mime', 120)->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->timestamps();

            $table->index(['project_id', 'category']);
        });

        Schema::create('mood_boards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('summary')->nullable();
            $table->unsignedSmallInteger('version')->default(1);
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->unique('project_id');
        });

        Schema::create('mood_board_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mood_board_id')->constrained()->cascadeOnDelete();
            $table->string('kind', 20);
            $table->string('title');
            $table->text('body')->nullable();
            $table->string('image')->nullable();
            $table->string('colour', 20)->nullable();
            $table->timestamps();
        });

        Schema::create('design_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mood_board_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('comment');
            $table->string('attachment')->nullable();
            $table->timestamps();
        });

        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contractor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('number', 40);
            $table->text('description');
            $table->decimal('materials', 12, 2)->default(0);
            $table->decimal('labour', 12, 2)->default(0);
            $table->decimal('additional_costs', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->date('valid_until')->nullable();
            $table->string('status', 30)->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['project_id', 'number']);
            $table->index(['project_id', 'status']);
        });

        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained()->cascadeOnDelete();
            $table->string('item');
            $table->text('description')->nullable();
            $table->decimal('quantity', 8, 2);
            $table->decimal('unit_cost', 12, 2);
            $table->decimal('total', 12, 2);
            $table->timestamps();
        });

        Schema::create('change_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->text('reason')->nullable();
            $table->string('category', 30);
            $table->string('priority', 20)->default('normal');
            $table->string('status', 30)->default('submitted');
            $table->text('contractor_response')->nullable();
            $table->text('designer_response')->nullable();
            $table->decimal('cost_impact', 12, 2)->nullable();
            $table->string('timeline_impact')->nullable();
            $table->string('attachment')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'status']);
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'created_at']);
        });

        Schema::create('message_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('original_name');
            $table->timestamps();
        });

        // Provider secrets and card data never belong here.
        // The gateway is attached later through provider_reference only.
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quotation_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reference', 40)->unique();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('LKR');
            $table->string('method', 40)->nullable();
            $table->string('status', 20)->default('pending');
            $table->string('provider_reference')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'status']);
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event', 60);
            $table->text('description');
            $table->json('properties')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'created_at']);
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('message_attachments');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('change_requests');
        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('quotations');
        Schema::dropIfExists('design_feedback');
        Schema::dropIfExists('mood_board_items');
        Schema::dropIfExists('mood_boards');
        Schema::dropIfExists('documents');
        Schema::dropIfExists('project_tasks');
        Schema::dropIfExists('milestones');
        Schema::dropIfExists('project_progress');
        Schema::dropIfExists('project_invitations');
        Schema::dropIfExists('project_reference_images');
        Schema::dropIfExists('professional_reviews');
        Schema::dropIfExists('portfolio_images');

        Schema::table('portfolio_items', function (Blueprint $table) {
            $table->dropColumn(['location', 'budget_min', 'budget_max']);
        });

        Schema::table('professional_profiles', function (Blueprint $table) {
            $table->dropColumn(['title', 'specialization', 'avatar_path', 'completed_projects_count', 'starting_price']);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'requirements',
                'additional_instructions',
                'timeline_notes',
                'current_budget',
                'actual_completion_date',
                'currency',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'address']);
        });
    }
};
