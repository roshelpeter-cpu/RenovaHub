<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Supplier, construction and contractor allocations carry their own
     * payment state. The parent row remains the homeowner project payment.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('net_amount', 12, 2)->nullable()->after('platform_fee');
        });

        Schema::table('payment_allocations', function (Blueprint $table) {
            $table->string('status', 20)->default('pending')->after('amount');
            $table->string('reference', 40)->nullable()->unique()->after('status');
            $table->string('label')->nullable()->after('reference');
            $table->string('detail')->nullable()->after('label');
            $table->decimal('platform_fee', 12, 2)->default(0)->after('detail');
            $table->decimal('net_amount', 12, 2)->nullable()->after('platform_fee');
            $table->foreignId('payer_id')->nullable()->after('payee_user_id')->constrained('users')->nullOnDelete();
            $table->string('provider', 40)->nullable()->after('payer_id');
            $table->string('provider_reference')->nullable()->after('provider');
            $table->timestamp('paid_at')->nullable()->after('provider_reference');
        });
    }

    public function down(): void
    {
        Schema::table('payment_allocations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payer_id');
            $table->dropColumn([
                'status',
                'reference',
                'label',
                'detail',
                'platform_fee',
                'net_amount',
                'provider',
                'provider_reference',
                'paid_at',
            ]);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('net_amount');
        });
    }
};
