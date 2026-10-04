<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Portfolio case studies need a long About, mood board and client note
     * per project. Homeowner projects need a size field for Key Details.
     */
    public function up(): void
    {
        Schema::table('professional_profiles', function (Blueprint $table) {
            $table->text('about')->nullable()->after('bio');
        });

        Schema::table('professional_projects', function (Blueprint $table) {
            $table->json('mood_board')->nullable()->after('process');
            $table->json('materials')->nullable()->after('mood_board');
            $table->string('client_name')->nullable()->after('materials');
            $table->string('client_location')->nullable()->after('client_name');
            $table->decimal('client_rating', 2, 1)->nullable()->after('client_location');
            $table->text('client_body')->nullable()->after('client_rating');
        });

        Schema::table('professional_project_images', function (Blueprint $table) {
            $table->string('caption')->nullable()->after('path');
            $table->string('image_type', 40)->nullable()->after('caption');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->unsignedInteger('size_sq_ft')->nullable()->after('property_type');
            $table->json('workspace_meta')->nullable()->after('timeline_notes');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['size_sq_ft', 'workspace_meta']);
        });

        Schema::table('professional_project_images', function (Blueprint $table) {
            $table->dropColumn(['caption', 'image_type']);
        });

        Schema::table('professional_projects', function (Blueprint $table) {
            $table->dropColumn(['mood_board', 'materials', 'client_name', 'client_location', 'client_rating', 'client_body']);
        });

        Schema::table('professional_profiles', function (Blueprint $table) {
            $table->dropColumn('about');
        });
    }
};
