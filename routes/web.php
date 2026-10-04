<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\Homeowner\ProfessionalController;
use App\Http\Controllers\Homeowner\ProjectController;
use App\Http\Controllers\Homeowner\SectionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Homeowner project flow. Policies reject other roles and other owners.
    Route::prefix('homeowner')->name('homeowner.')->group(function () {
        Route::get('sections/{section}', [SectionController::class, 'show'])
            ->whereIn('section', array_keys(SectionController::SECTIONS))
            ->name('sections.show');

        Route::get('professionals/{professional}', [ProfessionalController::class, 'show'])->name('professionals.show');

        Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
        Route::get('projects/create', [ProjectController::class, 'create'])->name('projects.create');
        Route::post('projects', [ProjectController::class, 'store'])->name('projects.store');
        Route::get('projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
        Route::get('projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
        Route::put('projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
        Route::delete('projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
        Route::get('projects/{project}/location', [ProjectController::class, 'location'])->name('projects.location');
        Route::put('projects/{project}/location', [ProjectController::class, 'updateLocation'])->name('projects.location.update');
        Route::get('projects/{project}/budget', [ProjectController::class, 'budget'])->name('projects.budget');
        Route::put('projects/{project}/budget', [ProjectController::class, 'updateBudget'])->name('projects.budget.update');
        Route::get('projects/{project}/team', [ProjectController::class, 'team'])->name('projects.team');
        Route::put('projects/{project}/team', [ProjectController::class, 'updateTeam'])->name('projects.team.update');
    });
});
Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])
    ->name('google.redirect');

Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])
    ->name('google.callback');

Route::view('/privacy-policy', 'marketing.legal', ['page' => 'privacy'])->name('privacy');
Route::view('/terms-of-service', 'marketing.legal', ['page' => 'terms'])->name('terms.public');