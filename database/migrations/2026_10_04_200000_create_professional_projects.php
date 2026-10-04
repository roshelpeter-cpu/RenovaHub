<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Case-study records power the professional profile gallery.
     * They are public marketing data, not the homeowner's private projects.
     */
    public function up(): void
    {
        Schema::table('professional_profiles', function (Blueprint $table) {
            $table->unsignedTinyInteger('client_satisfaction')->nullable()->after('completed_projects_count');
        });

        Schema::create('professional_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_profile_id')->constrained()->cascadeOnDelete();
            $table->string('slug');
            $table->string('title');
            $table->string('category')->nullable();
            $table->string('location')->nullable();
            $table->string('property_type')->nullable();
            $table->string('project_type')->nullable();
            $table->unsignedInteger('size_sq_ft')->nullable();
            $table->decimal('budget', 14, 2)->nullable();
            $table->date('completed_on')->nullable();
            $table->text('summary')->nullable();
            $table->text('overview')->nullable();
            $table->json('highlights')->nullable();
            $table->json('process')->nullable();
            $table->string('hero_image');
            $table->boolean('featured')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['professional_profile_id', 'slug']);
        });

        Schema::create('professional_project_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_project_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('professional_project_images');
        Schema::dropIfExists('professional_projects');

        Schema::table('professional_profiles', function (Blueprint $table) {
            $table->dropColumn('client_satisfaction');
        });
    }
};
