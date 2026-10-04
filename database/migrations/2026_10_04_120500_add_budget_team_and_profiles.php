<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Budget, timeline, team and portfolio tables extend the existing project.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->decimal('estimated_budget', 12, 2)->nullable()->after('longitude');
            $table->date('expected_start_date')->nullable()->after('estimated_budget');
            $table->date('expected_completion_date')->nullable()->after('expected_start_date');
            $table->unsignedTinyInteger('progress')->nullable()->after('status');
            $table->string('cover_image')->nullable()->after('progress');
            $table->foreignId('designer_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            $table->foreignId('contractor_id')->nullable()->after('designer_id')->constrained('users')->nullOnDelete();
        });

        Schema::create('professional_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('professional_type', 30);
            $table->string('business_name')->nullable();
            $table->text('bio');
            $table->string('location')->nullable();
            $table->unsignedTinyInteger('years_experience')->nullable();
            $table->decimal('rating', 2, 1)->nullable();
            $table->timestamps();

            $table->unique('user_id');
            $table->index('professional_type');
        });

        Schema::create('portfolio_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_profile_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->string('image');
            $table->string('category', 80);
            $table->unsignedSmallInteger('completion_year')->nullable();
            $table->timestamps();

            $table->index(['professional_profile_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_items');
        Schema::dropIfExists('professional_profiles');

        Schema::table('projects', function (Blueprint $table) {
            $table->dropConstrainedForeignId('contractor_id');
            $table->dropConstrainedForeignId('designer_id');
            $table->dropColumn([
                'estimated_budget',
                'expected_start_date',
                'expected_completion_date',
                'progress',
                'cover_image',
            ]);
        });
    }
};
