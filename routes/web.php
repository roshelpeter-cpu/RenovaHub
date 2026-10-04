<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\Homeowner\ChangeRequestController;
use App\Http\Controllers\Homeowner\DocumentController;
use App\Http\Controllers\Homeowner\ConversationController;
use App\Http\Controllers\Homeowner\HomeController;
use App\Http\Controllers\Homeowner\MessageController;
use App\Http\Controllers\Homeowner\MoodBoardController;
use App\Http\Controllers\Homeowner\NotificationController;
use App\Http\Controllers\Homeowner\PaymentController;
use App\Http\Controllers\Homeowner\ProfessionalController;
use App\Http\Controllers\Homeowner\ProfileController;
use App\Http\Controllers\Homeowner\ProjectController;
use App\Http\Controllers\Homeowner\QuotationController;
use App\Http\Controllers\Homeowner\SectionController;
use App\Http\Controllers\Homeowner\TaskController;
use App\Http\Controllers\InvitationController;
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

    Route::get('invitations/{invitation}', [InvitationController::class, 'show'])->name('invitations.show');
    Route::post('invitations/{invitation}', [InvitationController::class, 'respond'])->name('invitations.respond');

    // Homeowner project flow. The homeowner middleware and policies both reject other roles.
    Route::prefix('homeowner')->middleware('homeowner')->name('homeowner.')->group(function () {
        Route::get('/', HomeController::class)->name('home');
        Route::get('explore', [ProfessionalController::class, 'index'])->name('explore');

        Route::get('sections/{section}', [SectionController::class, 'show'])
            ->whereIn('section', array_keys(SectionController::SECTIONS))
            ->name('sections.show');

        Route::get('professionals/{professional}', [ProfessionalController::class, 'show'])->name('professionals.show');
        Route::get('professionals/{professional}/projects/{caseStudy}', [ProfessionalController::class, 'project'])->name('professionals.project');
        Route::post('professionals/{professional}/favourite', [ProfessionalController::class, 'favourite'])->name('professionals.favourite');
        Route::post('professionals/{professional}/contact', [ProfessionalController::class, 'contact'])->name('professionals.contact');

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

        Route::get('tasks', [TaskController::class, 'index'])->name('tasks.index');
        Route::get('projects/{project}/tasks', [TaskController::class, 'project'])->name('projects.tasks');
        Route::get('projects/{project}/tasks/{task}', [TaskController::class, 'show'])->name('projects.tasks.show');

        Route::get('documents', [DocumentController::class, 'index'])->name('documents.index');
        Route::get('projects/{project}/documents', [DocumentController::class, 'project'])->name('projects.documents');
        Route::post('projects/{project}/documents', [DocumentController::class, 'store'])->name('projects.documents.store');
        Route::get('projects/{project}/documents/{document}/download', [DocumentController::class, 'download'])->name('projects.documents.download');
        Route::delete('projects/{project}/documents/{document}', [DocumentController::class, 'destroy'])->name('projects.documents.destroy');

        Route::get('mood-board', [MoodBoardController::class, 'index'])->name('mood-board.index');
        Route::get('projects/{project}/mood-board', [MoodBoardController::class, 'show'])->name('projects.mood-board');
        Route::post('projects/{project}/mood-board/feedback', [MoodBoardController::class, 'feedback'])->name('projects.mood-board.feedback');
        Route::post('projects/{project}/mood-board/approve', [MoodBoardController::class, 'approve'])->name('projects.mood-board.approve');

        Route::get('quotations', [QuotationController::class, 'index'])->name('quotations.index');
        Route::get('projects/{project}/quotations', [QuotationController::class, 'project'])->name('projects.quotations');
        Route::get('projects/{project}/quotations/{quotation}', [QuotationController::class, 'show'])->name('quotations.show');
        Route::post('projects/{project}/quotations/{quotation}/decide', [QuotationController::class, 'decide'])->name('quotations.decide');

        Route::get('change-requests', [ChangeRequestController::class, 'index'])->name('change-requests.index');
        Route::get('projects/{project}/change-requests', [ChangeRequestController::class, 'project'])->name('projects.change-requests');
        Route::get('projects/{project}/change-requests/create', [ChangeRequestController::class, 'create'])->name('projects.change-requests.create');
        Route::post('projects/{project}/change-requests', [ChangeRequestController::class, 'store'])->name('projects.change-requests.store');
        Route::get('projects/{project}/change-requests/{changeRequest}', [ChangeRequestController::class, 'show'])->name('change-requests.show');

        Route::get('messages', [ConversationController::class, 'index'])->name('messages.index');
        Route::get('messages/{conversation}', [ConversationController::class, 'show'])->name('messages.show');
        Route::get('projects/{project}/messages', [MessageController::class, 'show'])->name('projects.messages');
        Route::post('projects/{project}/messages', [MessageController::class, 'store'])->name('projects.messages.store');

        Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('projects/{project}/activity', [ProjectController::class, 'activity'])->name('projects.activity');
        Route::get('projects/{project}/payments', [PaymentController::class, 'project'])->name('projects.payments');
        Route::get('projects/{project}/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
        Route::get('projects/{project}/payments/{payment}/pay', [PaymentController::class, 'pay'])->name('payments.pay');

        Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
        Route::post('notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');

        Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    });
});
Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])
    ->name('google.redirect');

Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])
    ->name('google.callback');

Route::view('/privacy-policy', 'marketing.legal', ['page' => 'privacy'])->name('privacy');
Route::view('/terms-of-service', 'marketing.legal', ['page' => 'terms'])->name('terms.public');