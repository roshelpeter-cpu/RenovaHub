<?php

use App\Http\Controllers\Contractor\TaskController as ContractorTaskController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Designer\DashboardController as DesignerDashboardController;
use App\Http\Controllers\Designer\DesignConceptController;
use App\Http\Controllers\Designer\DocumentController as DesignerDocumentController;
use App\Http\Controllers\Designer\EarningController;
use App\Http\Controllers\Designer\InvitationController as DesignerInvitationController;
use App\Http\Controllers\Designer\MessageController as DesignerMessageController;
use App\Http\Controllers\Designer\MoodBoardController as DesignerMoodBoardController;
use App\Http\Controllers\Designer\NotificationController as DesignerNotificationController;
use App\Http\Controllers\Designer\ProfileController as DesignerProfileController;
use App\Http\Controllers\Designer\ProjectController as DesignerProjectController;
use App\Http\Controllers\Designer\RevisionController;
use App\Http\Controllers\Designer\TaskController as DesignerTaskController;
use App\Http\Controllers\Homeowner\DesignReviewController;
use App\Http\Controllers\Homeowner\FeedbackController;
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

    // Outside the homeowner group: homeowners are denied by the task policy.
    Route::post('projects/{project}/tasks', [ContractorTaskController::class, 'store'])->name('projects.tasks.store');

    // Homeowner project flow. The homeowner middleware and policies both reject other roles.
    Route::prefix('homeowner')->middleware('homeowner')->name('homeowner.')->group(function () {
        Route::get('/', HomeController::class)->name('home');
        Route::get('explore', [ProfessionalController::class, 'index'])->name('explore');

        Route::get('sections/{section}', [SectionController::class, 'show'])
            ->whereIn('section', array_keys(SectionController::SECTIONS))
            ->name('sections.show');

        Route::get('professionals/{professional}', [ProfessionalController::class, 'show'])->name('professionals.show');
        Route::get('professionals/{professional}/portfolio', [ProfessionalController::class, 'portfolio'])->name('professionals.portfolio');
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
        Route::post('documents', [DocumentController::class, 'storeGlobal'])->name('documents.store');
        Route::get('projects/{project}/documents', [DocumentController::class, 'project'])->name('projects.documents');
        Route::post('projects/{project}/documents', [DocumentController::class, 'store'])->name('projects.documents.store');
        Route::get('projects/{project}/documents/{document}', [DocumentController::class, 'show'])->name('projects.documents.show');
        Route::get('projects/{project}/documents/{document}/download', [DocumentController::class, 'download'])->name('projects.documents.download');
        Route::delete('projects/{project}/documents/{document}', [DocumentController::class, 'destroy'])->name('projects.documents.destroy');

        Route::get('mood-board', [MoodBoardController::class, 'index'])->name('mood-board.index');
        Route::post('mood-board', [MoodBoardController::class, 'storeItem'])->name('mood-board.store');
        Route::get('projects/{project}/mood-board', [MoodBoardController::class, 'show'])->name('projects.mood-board');
        Route::post('projects/{project}/mood-board/items', [MoodBoardController::class, 'storeItem'])->name('projects.mood-board.items.store');
        Route::post('projects/{project}/mood-board/feedback', [MoodBoardController::class, 'feedback'])->name('projects.mood-board.feedback');
        Route::post('projects/{project}/mood-board/approve', [MoodBoardController::class, 'approve'])->name('projects.mood-board.approve');
        Route::post('projects/{project}/mood-board/request-changes', [MoodBoardController::class, 'requestChanges'])->name('projects.mood-board.request-changes');
        Route::post('projects/{project}/design-changes', [DesignReviewController::class, 'storeChange'])->name('projects.design-changes.store');
        Route::post('projects/{project}/concepts/{concept}/decide', [DesignReviewController::class, 'decideConcept'])->name('concepts.decide');

        Route::get('quotations', [QuotationController::class, 'index'])->name('quotations.index');
        Route::get('projects/{project}/quotations', [QuotationController::class, 'project'])->name('projects.quotations');
        Route::get('projects/{project}/quotations/{quotation}', [QuotationController::class, 'show'])->name('quotations.show');
        Route::post('projects/{project}/quotations/{quotation}/decide', [QuotationController::class, 'decide'])->name('quotations.decide');

        Route::get('change-requests', [ChangeRequestController::class, 'index'])->name('change-requests.index');
        Route::get('projects/{project}/change-requests', [ChangeRequestController::class, 'project'])->name('projects.change-requests');
        Route::get('projects/{project}/change-requests/create', [ChangeRequestController::class, 'create'])->name('projects.change-requests.create');
        Route::post('projects/{project}/change-requests', [ChangeRequestController::class, 'store'])->name('projects.change-requests.store');
        Route::get('projects/{project}/change-requests/{changeRequest}', [ChangeRequestController::class, 'show'])->name('change-requests.show');

        Route::get('projects/{project}/feedback', [FeedbackController::class, 'show'])->name('projects.feedback');
        Route::post('projects/{project}/feedback', [FeedbackController::class, 'store'])->name('projects.feedback.store');

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

    // Designer workspace. Membership is checked again in policies, not only by this middleware.
    Route::prefix('designer')->middleware('designer')->name('designer.')->group(function () {
        Route::get('dashboard', DesignerDashboardController::class)->name('dashboard');

        Route::get('invitations', [DesignerInvitationController::class, 'index'])->name('invitations.index');
        Route::post('invitations/{invitation}/accept', [DesignerInvitationController::class, 'accept'])->name('invitations.accept');
        Route::post('invitations/{invitation}/reject', [DesignerInvitationController::class, 'reject'])->name('invitations.reject');

        Route::get('projects', [DesignerProjectController::class, 'index'])->name('projects.index');
        Route::get('design-work', [DesignerProjectController::class, 'designWork'])->name('design-work');
        Route::get('projects/{project}', [DesignerProjectController::class, 'show'])->name('projects.show');
        Route::get('projects/{project}/brief', [DesignerProjectController::class, 'brief'])->name('projects.brief');

        Route::get('mood-boards', [DesignerMoodBoardController::class, 'index'])->name('mood-boards.index');
        Route::get('projects/{project}/mood-board', [DesignerMoodBoardController::class, 'show'])->name('projects.mood-board');
        Route::post('projects/{project}/mood-board/items', [DesignerMoodBoardController::class, 'storeItem'])->name('projects.mood-board.items.store');
        Route::post('projects/{project}/mood-board/submit', [DesignerMoodBoardController::class, 'submit'])->name('projects.mood-board.submit');

        Route::get('design-concepts', [DesignConceptController::class, 'index'])->name('concepts.index');
        Route::post('projects/{project}/concepts', [DesignConceptController::class, 'store'])->name('concepts.store');
        Route::get('projects/{project}/concepts/{concept}', [DesignConceptController::class, 'show'])->name('concepts.show');
        Route::put('projects/{project}/concepts/{concept}', [DesignConceptController::class, 'update'])->name('concepts.update');
        Route::post('projects/{project}/concepts/{concept}/submit', [DesignConceptController::class, 'submit'])->name('concepts.submit');

        Route::get('revisions', [RevisionController::class, 'index'])->name('revisions.index');
        Route::get('projects/{project}/revisions', [RevisionController::class, 'project'])->name('projects.revisions');
        Route::post('revisions/{revision}/accept', [RevisionController::class, 'accept'])->name('revisions.accept');
        Route::post('revisions/{revision}/reject', [RevisionController::class, 'reject'])->name('revisions.reject');
        Route::post('revisions/{revision}/resubmit', [RevisionController::class, 'resubmit'])->name('revisions.resubmit');

        Route::get('documents', [DesignerDocumentController::class, 'index'])->name('documents.index');
        Route::get('projects/{project}/documents', [DesignerDocumentController::class, 'project'])->name('projects.documents');
        Route::post('documents', [DesignerDocumentController::class, 'store'])->name('documents.store');
        Route::get('documents/{document}', [DesignerDocumentController::class, 'show'])->name('documents.show');
        Route::get('documents/{document}/download', [DesignerDocumentController::class, 'download'])->name('documents.download');
        Route::delete('documents/{document}', [DesignerDocumentController::class, 'destroy'])->name('documents.destroy');

        Route::get('tasks', [DesignerTaskController::class, 'index'])->name('tasks.index');
        Route::get('projects/{project}/tasks', [DesignerTaskController::class, 'project'])->name('projects.tasks');
        Route::put('tasks/{task}', [DesignerTaskController::class, 'update'])->name('tasks.update');

        Route::get('messages', [DesignerMessageController::class, 'index'])->name('messages.index');
        Route::get('messages/{conversation}', [DesignerMessageController::class, 'show'])->name('messages.show');
        Route::get('projects/{project}/messages', [DesignerMessageController::class, 'project'])->name('projects.messages');

        Route::get('earnings', [EarningController::class, 'index'])->name('earnings.index');
        Route::get('earnings/{earning}', [EarningController::class, 'show'])->name('earnings.show');

        Route::get('profile', [DesignerProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [DesignerProfileController::class, 'update'])->name('profile.update');
        Route::post('profile/portfolio', [DesignerProfileController::class, 'storePortfolio'])->name('profile.portfolio.store');
        Route::get('profile/projects/{caseStudy}', [DesignerProfileController::class, 'project'])->name('profile.project');

        Route::get('notifications', [DesignerNotificationController::class, 'index'])->name('notifications.index');
        Route::post('notifications/{notification}/read', [DesignerNotificationController::class, 'read'])->name('notifications.read');
    });
});
Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])
    ->name('google.redirect');

Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])
    ->name('google.callback');

Route::view('/privacy-policy', 'marketing.legal', ['page' => 'privacy'])->name('privacy');
Route::view('/terms-of-service', 'marketing.legal', ['page' => 'terms'])->name('terms.public');