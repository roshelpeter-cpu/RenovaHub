<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Public directory fields for Explore. Favourites are stored per homeowner
     * so a client cannot favourite on someone else's account by posting an id.
     */
    public function up(): void
    {
        Schema::table('professional_profiles', function (Blueprint $table) {
            $table->boolean('listed')->default(false)->after('starting_price');
            $table->boolean('featured')->default(false)->after('listed');
            $table->boolean('verified')->default(false)->after('featured');
            $table->unsignedInteger('review_count')->default(0)->after('rating');
            $table->json('tags')->nullable()->after('specialization');
            $table->string('cover_path')->nullable()->after('avatar_path');
        });

        Schema::create('professional_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_profile_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('professional_favourites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('professional_profile_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'professional_profile_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('professional_favourites');
        Schema::dropIfExists('professional_services');

        Schema::table('professional_profiles', function (Blueprint $table) {
            $table->dropColumn(['listed', 'featured', 'verified', 'review_count', 'tags', 'cover_path']);
        });
    }
};
