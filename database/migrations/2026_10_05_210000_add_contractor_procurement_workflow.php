<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Contractor procurement sits on top of the approved designer concept.
 * The homeowner still approves the supplier, the construction firm,
 * and the single project budget before any allocation is recorded.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('design_concept_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('room')->nullable();
            $table->string('category')->nullable();
            $table->decimal('quantity', 12, 2)->default(1);
            $table->string('unit')->default('unit');
            $table->string('specification')->nullable();
            $table->decimal('estimated_value', 12, 2)->default(0);
            $table->string('status')->default('pending')->index();
            $table->timestamps();
            $table->index(['project_id', 'category']);
        });

        Schema::create('construction_firms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('city')->nullable();
            $table->string('location')->nullable();
            $table->string('specialisation')->nullable();
            $table->text('about')->nullable();
            $table->decimal('rating', 3, 1)->default(0);
            $table->unsignedInteger('review_count')->default(0);
            $table->unsignedInteger('years_experience')->default(0);
            $table->unsignedInteger('completed_projects')->default(0);
            $table->decimal('starting_price', 12, 2)->default(0);
            $table->json('portfolio')->nullable();
            $table->json('highlights')->nullable();
            $table->timestamps();
        });

        Schema::create('construction_firm_quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contractor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('construction_firm_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 12, 2);
            $table->unsignedInteger('duration_days');
            $table->date('start_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->text('scope')->nullable();
            $table->text('terms')->nullable();
            $table->text('notes')->nullable();
            $table->string('warranty')->nullable();
            // received until the contractor sends the comparison to the homeowner.
            $table->string('status')->default('received')->index();
            $table->timestamps();
            $table->index(['project_id', 'status']);
        });

        Schema::create('construction_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('construction_firm_id')->constrained()->cascadeOnDelete();
            $table->foreignId('construction_firm_quotation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('assigned_at');
            $table->timestamps();
            $table->unique('project_id');
        });

        Schema::create('procurement_proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contractor_id')->constrained('users')->cascadeOnDelete();
            $table->string('type')->index();
            $table->string('status')->default('awaiting_homeowner')->index();
            $table->foreignId('selected_supplier_price_id')->nullable()->constrained('supplier_prices')->nullOnDelete();
            $table->foreignId('selected_construction_quotation_id')->nullable()->constrained('construction_firm_quotations')->nullOnDelete();
            $table->text('homeowner_note')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();
        });

        Schema::create('procurement_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('procurement_proposal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_price_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('construction_firm_quotation_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('budget_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contractor_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('designer_fee', 12, 2)->default(0);
            $table->decimal('materials', 12, 2)->default(0);
            $table->decimal('construction', 12, 2)->default(0);
            $table->decimal('contractor_fee', 12, 2)->default(0);
            $table->decimal('changes', 12, 2)->default(0);
            $table->decimal('platform_fee', 12, 2)->default(0);
            $table->decimal('fee_percent', 5, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->string('status')->default('draft')->index();
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();
            $table->index(['project_id', 'status']);
        });

        Schema::create('payment_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('bucket')->index();
            $table->decimal('amount', 12, 2);
            $table->foreignId('payee_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('construction_firm_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('budget_submission_id')->nullable()->after('supplier_order_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('budget_submission_id');
        });

        Schema::dropIfExists('payment_allocations');
        Schema::dropIfExists('budget_submissions');
        Schema::dropIfExists('procurement_options');
        Schema::dropIfExists('procurement_proposals');
        Schema::dropIfExists('construction_assignments');
        Schema::dropIfExists('construction_firm_quotations');
        Schema::dropIfExists('construction_firms');
        Schema::dropIfExists('material_requirements');
    }
};
