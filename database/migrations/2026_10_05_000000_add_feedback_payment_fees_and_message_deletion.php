<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Feedback is a completed-project record, separate from mood-board notes.
     * Payment fee columns record the RenovaHub charge without storing card data.
     * Message deletion is a soft delete so the sender check can stay auditable.
     */
    public function up(): void
    {
        Schema::create('project_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('homeowner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('professional_id')->constrained('users')->cascadeOnDelete();
            $table->string('role', 20);
            $table->unsignedTinyInteger('rating');
            $table->string('title');
            $table->text('comment');
            $table->string('attachment')->nullable();
            $table->timestamps();

            $table->unique(['project_id', 'role']);
            $table->index('professional_id');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('renovation_amount', 12, 2)->nullable()->after('amount');
            $table->decimal('platform_fee', 12, 2)->nullable()->after('renovation_amount');
            $table->decimal('fee_percent', 5, 2)->default(2)->after('platform_fee');
        });

        Schema::table('change_requests', function (Blueprint $table) {
            $table->timestamp('approved_at')->nullable()->after('status');
        });

        Schema::table('conversation_messages', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('conversation_messages', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('change_requests', function (Blueprint $table) {
            $table->dropColumn('approved_at');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['renovation_amount', 'platform_fee', 'fee_percent']);
        });

        Schema::dropIfExists('project_feedback');
    }
};
