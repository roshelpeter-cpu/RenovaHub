<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Suppliers are a catalogue the contractor buys from. They are not a fourth
     * user role. Orders and price requests hang off the existing projects table
     * so a contractor cannot attach a purchase to a project they do not hold.
     */
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo_path')->nullable();
            $table->string('hero_path')->nullable();
            $table->string('location');
            $table->string('city')->nullable();
            $table->string('service_area')->nullable();
            $table->text('about')->nullable();
            $table->unsignedSmallInteger('years_experience')->default(0);
            $table->unsignedInteger('completed_orders')->default(0);
            $table->decimal('rating', 2, 1)->default(0);
            $table->unsignedInteger('review_count')->default(0);
            $table->json('why_choose')->nullable();
            $table->json('portfolio_images')->nullable();
            $table->timestamps();

            $table->index('city');
        });

        Schema::create('supplier_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('supplier_category_supplier', function (Blueprint $table) {
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_category_id')->constrained()->cascadeOnDelete();
            $table->primary(['supplier_id', 'supplier_category_id']);
        });

        Schema::create('supplier_starting_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->decimal('amount', 12, 2);
            $table->string('unit', 40)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('supplier_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->string('author_name');
            $table->string('project_title')->nullable();
            $table->text('body');
            $table->decimal('rating', 2, 1);
            $table->timestamps();
        });

        Schema::create('supplier_price_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contractor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->string('material');
            $table->string('product');
            $table->decimal('quantity', 10, 2);
            $table->string('unit', 40);
            $table->date('required_by')->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 30)->default('sent');
            $table->timestamps();

            $table->index(['contractor_id', 'status']);
            $table->index(['project_id', 'supplier_id']);
        });

        Schema::create('supplier_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_price_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->decimal('quoted_price', 12, 2);
            $table->unsignedSmallInteger('lead_time_days')->nullable();
            $table->string('delivery', 80)->nullable();
            $table->string('warranty', 80)->nullable();
            $table->date('valid_until')->nullable();
            $table->text('message')->nullable();
            $table->string('status', 30)->default('offered');
            $table->timestamps();

            $table->index(['supplier_price_request_id', 'status']);
        });

        Schema::create('supplier_orders', function (Blueprint $table) {
            $table->id();
            $table->string('number', 40)->unique();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contractor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_price_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('supplier_price_request_id')->nullable()->constrained()->nullOnDelete();
            $table->string('item');
            $table->decimal('quantity', 10, 2);
            $table->string('unit', 40);
            $table->decimal('amount', 12, 2);
            $table->string('delivery_address')->nullable();
            $table->date('required_delivery_date')->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 30)->default('payment_pending');
            $table->timestamp('ordered_at')->nullable();
            $table->timestamps();

            $table->index(['contractor_id', 'status']);
            $table->index(['project_id', 'status']);
        });

        Schema::create('contractor_earnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contractor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('supplier_order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('label');
            $table->decimal('gross_amount', 12, 2);
            $table->decimal('fee_percent', 5, 2);
            $table->decimal('fee_amount', 12, 2);
            $table->decimal('net_amount', 12, 2);
            $table->string('status', 20)->default('pending');
            $table->string('reference', 40)->nullable();
            $table->date('recorded_on')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['contractor_id', 'status']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('supplier_order_id')->nullable()->after('quotation_id')->constrained()->nullOnDelete();
            $table->foreignId('payer_id')->nullable()->after('supplier_order_id')->constrained('users')->nullOnDelete();
            $table->foreignId('payee_id')->nullable()->after('payer_id')->constrained('users')->nullOnDelete();
            $table->string('provider', 40)->default('payhere')->after('method');
        });

        Schema::table('quotation_items', function (Blueprint $table) {
            $table->string('category', 30)->default('materials')->after('item');
        });

        Schema::table('project_tasks', function (Blueprint $table) {
            $table->string('assignee_label')->nullable()->after('assignee_id');
            $table->text('notes')->nullable()->after('description');
        });

        Schema::table('change_requests', function (Blueprint $table) {
            $table->string('feasibility', 40)->nullable()->after('timeline_impact');
            $table->boolean('design_affected')->default(false)->after('feasibility');
        });
    }

    public function down(): void
    {
        Schema::table('change_requests', function (Blueprint $table) {
            $table->dropColumn(['feasibility', 'design_affected']);
        });

        Schema::table('project_tasks', function (Blueprint $table) {
            $table->dropColumn(['assignee_label', 'notes']);
        });

        Schema::table('quotation_items', function (Blueprint $table) {
            $table->dropColumn('category');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payee_id');
            $table->dropConstrainedForeignId('payer_id');
            $table->dropConstrainedForeignId('supplier_order_id');
            $table->dropColumn('provider');
        });

        Schema::dropIfExists('contractor_earnings');
        Schema::dropIfExists('supplier_orders');
        Schema::dropIfExists('supplier_prices');
        Schema::dropIfExists('supplier_price_requests');
        Schema::dropIfExists('supplier_reviews');
        Schema::dropIfExists('supplier_starting_prices');
        Schema::dropIfExists('supplier_category_supplier');
        Schema::dropIfExists('supplier_categories');
        Schema::dropIfExists('suppliers');
    }
};
