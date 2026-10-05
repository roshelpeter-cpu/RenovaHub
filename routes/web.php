<?php

use App\Http\Controllers\Contractor\BudgetController as ContractorBudgetController;
use App\Http\Controllers\Contractor\ChangeRequestController as ContractorChangeRequestController;
use App\Http\Controllers\Contractor\ConstructionFirmController;
use App\Http\Controllers\Contractor\DashboardController as ContractorDashboardController;
use App\Http\Controllers\Contractor\DocumentController as ContractorDocumentController;
use App\Http\Controllers\Contractor\EarningsController as ContractorEarningsController;
use App\Http\Controllers\Contractor\FinalDesignController;
use App\Http\Controllers\Contractor\InvitationController as ContractorInvitationController;
use App\Http\Controllers\Contractor\MaterialRequirementController;
use App\Http\Controllers\Contractor\MessageController as ContractorMessageController;
use App\Http\Controllers\Contractor\NotificationController as ContractorNotificationController;
use App\Http\Controllers\Contractor\ProcurementController as ContractorProcurementController;
use App\Http\Controllers\Contractor\ProfileController as ContractorProfileController;
use App\Http\Controllers\Contractor\ProjectController as ContractorProjectController;
use App\Http\Controllers\Contractor\QuotationController as ContractorQuotationController;
use App\Http\Controllers\Contractor\SupplierController as ContractorSupplierController;
use App\Http\Controllers\Contractor\SupplierOrderController as ContractorSupplierOrderController;
use App\Http\Controllers\Contractor\SupplierPriceRequestController as ContractorSupplierPriceRequestController;
use App\Http\Controllers\Contractor\TaskController as ContractorTaskController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Designer\DashboardController as DesignerDashboardController;
use App\Http\Controllers\Designer\DesignConceptController;
use App\Http\Controllers\Designer\DocumentController as DesignerDocumentController;
use App\Http\Controllers\Designer\EarningController;
use App\Http\Controllers\Designer\FinalDesignController as DesignerFinalDesignController;
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
use App\Http\Controllers\Homeowner\FinalDesignController as HomeownerFinalDesignController;
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
use App\Http\Controllers\Homeowner\ProcurementDecisionController;
use App\Http\Controllers\Homeowner\ProfileController;
use App\Http\Controllers\Homeowner\ProjectController;
use App\Http\Controllers\Homeowner\QuotationController;
use App\Http\Controllers\Homeowner\SectionController;
use App\Http\Controllers\Homeowner\TaskController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\PayHereNotifyController;
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
        Route::get('projects/{project}/final-design', [HomeownerFinalDesignController::class, 'show'])->name('projects.final-design');
        Route::post('projects/{project}/final-design/approve', [HomeownerFinalDesignController::class, 'approve'])->name('projects.final-design.approve');
        Route::post('projects/{project}/final-design/request-changes', [HomeownerFinalDesignController::class, 'requestChanges'])->name('projects.final-design.request-changes');
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
        Route::post('proposals/{proposal}/decide', [ProcurementDecisionController::class, 'proposal'])->name('proposals.decide');
        Route::post('budget-submissions/{submission}/decide', [ProcurementDecisionController::class, 'budget'])->name('budget-submissions.decide');
        Route::get('projects/{project}/activity', [ProjectController::class, 'activity'])->name('projects.activity');
        Route::get('projects/{project}/payments', [PaymentController::class, 'project'])->name('projects.payments');
        Route::get('projects/{project}/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
        Route::get('projects/{project}/payments/{payment}/pay', [PaymentController::class, 'pay'])->name('payments.pay');
        Route::post('projects/{project}/payments/{payment}/confirm', [PaymentController::class, 'confirm'])->name('payments.confirm');
        Route::get('projects/{project}/payments/{payment}/result', [PaymentController::class, 'result'])->name('payments.result');
        Route::get('payments/payhere/return/{payment}', [PaymentController::class, 'returned'])->name('payments.payhere.return');
        Route::get('payments/payhere/cancel/{payment}', [PaymentController::class, 'cancelled'])->name('payments.payhere.cancel');

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
        Route::get('invitations/{invitation}', [DesignerInvitationController::class, 'show'])->name('invitations.show');
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

        Route::get('projects/{project}/final-design', [DesignerFinalDesignController::class, 'show'])->name('projects.final-design');
        Route::post('projects/{project}/final-design/files', [DesignerFinalDesignController::class, 'storeFile'])->name('projects.final-design.files');
        Route::post('projects/{project}/final-design/materials', [DesignerFinalDesignController::class, 'storeMaterial'])->name('projects.final-design.materials');
        Route::post('projects/{project}/final-design/submit', [DesignerFinalDesignController::class, 'submit'])->name('projects.final-design.submit');
        Route::post('final-design/changes/{change}', [DesignerFinalDesignController::class, 'respond'])->name('final-design.changes.respond');

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

    // Contractor workspace. Policies check the assigned contractor again, not only this middleware.
    Route::prefix('contractor')->middleware('contractor')->name('contractor.')->group(function () {
        Route::get('dashboard', ContractorDashboardController::class)->name('dashboard');

        Route::get('invitations', [ContractorInvitationController::class, 'index'])->name('invitations.index');
        Route::get('invitations/{invitation}', [ContractorInvitationController::class, 'show'])->name('invitations.show');
        Route::post('invitations/{invitation}/accept', [ContractorInvitationController::class, 'accept'])->name('invitations.accept');
        Route::post('invitations/{invitation}/reject', [ContractorInvitationController::class, 'reject'])->name('invitations.reject');

        Route::get('final-designs', [FinalDesignController::class, 'index'])->name('final-designs.index');
        Route::get('final-designs/{project}', [FinalDesignController::class, 'show'])->name('final-designs.show');
        Route::get('materials', [MaterialRequirementController::class, 'index'])->name('materials.index');

        Route::get('construction-firms', [ConstructionFirmController::class, 'index'])->name('firms.index');
        Route::get('construction-firms/{firm}', [ConstructionFirmController::class, 'show'])->name('firms.show');
        Route::get('construction-firms/{firm}/quote', [ConstructionFirmController::class, 'quote'])->name('firms.quote');
        Route::post('construction-firms/{firm}/quote', [ConstructionFirmController::class, 'storeQuote'])->name('firms.quote.store');

        Route::get('projects/{project}/compare/suppliers', [ContractorProcurementController::class, 'suppliers'])->name('procurement.suppliers');
        Route::post('projects/{project}/compare/suppliers', [ContractorProcurementController::class, 'sendSuppliers'])->name('procurement.suppliers.send');
        Route::get('projects/{project}/compare/firms', [ContractorProcurementController::class, 'firms'])->name('procurement.firms');
        Route::post('projects/{project}/compare/firms', [ContractorProcurementController::class, 'sendFirms'])->name('procurement.firms.send');
        Route::post('projects/{project}/firms/{quotation}/assign', [ContractorProcurementController::class, 'assign'])->name('firms.assign');

        Route::get('projects', [ContractorProjectController::class, 'index'])->name('projects.index');
        Route::get('projects/{project}', [ContractorProjectController::class, 'show'])->name('projects.show');
        Route::get('projects/{project}/messages', [ContractorMessageController::class, 'project'])->name('projects.messages');
        Route::get('projects/{project}/{section}', [ContractorProjectController::class, 'section'])
            ->whereIn('section', ['quotations', 'budget', 'suppliers', 'tasks', 'documents', 'change-requests'])
            ->name('projects.section');

        Route::get('quotations', [ContractorQuotationController::class, 'index'])->name('quotations.index');
        Route::get('quotations/create', [ContractorQuotationController::class, 'create'])->name('quotations.create');
        Route::post('quotations', [ContractorQuotationController::class, 'store'])->name('quotations.store');
        Route::get('quotations/{quotation}', [ContractorQuotationController::class, 'show'])->name('quotations.show');
        Route::get('quotations/{quotation}/edit', [ContractorQuotationController::class, 'edit'])->name('quotations.edit');
        Route::put('quotations/{quotation}', [ContractorQuotationController::class, 'update'])->name('quotations.update');
        Route::post('quotations/{quotation}/submit', [ContractorQuotationController::class, 'submit'])->name('quotations.submit');

        Route::get('budget', [ContractorBudgetController::class, 'index'])->name('budget.index');
        Route::put('budget/{project}', [ContractorBudgetController::class, 'update'])->name('budget.update');
        Route::post('budget/{project}/submit', [ContractorBudgetController::class, 'submit'])->name('budget.submit');

        Route::get('suppliers', [ContractorSupplierController::class, 'index'])->name('suppliers.index');
        Route::get('suppliers/{supplier}', [ContractorSupplierController::class, 'show'])->name('suppliers.show');
        Route::get('suppliers/{supplier}/request-price', [ContractorSupplierPriceRequestController::class, 'create'])->name('suppliers.request-price');
        Route::post('suppliers/{supplier}/request-price', [ContractorSupplierPriceRequestController::class, 'store'])->name('suppliers.request-price.store');
        Route::get('prices/{price}', [ContractorSupplierPriceRequestController::class, 'show'])->name('prices.show');
        Route::post('prices/{price}/revision', [ContractorSupplierPriceRequestController::class, 'revise'])->name('prices.revision');

        Route::get('orders', [ContractorSupplierOrderController::class, 'index'])->name('orders.index');
        Route::post('prices/{price}/order', [ContractorSupplierOrderController::class, 'store'])->name('orders.store');
        Route::get('orders/{order}', [ContractorSupplierOrderController::class, 'show'])->name('orders.show');
        Route::put('orders/{order}', [ContractorSupplierOrderController::class, 'update'])->name('orders.update');

        Route::get('tasks', [ContractorTaskController::class, 'index'])->name('tasks.index');
        Route::post('tasks', [ContractorTaskController::class, 'storeForWorkspace'])->name('tasks.store');
        Route::put('tasks/{task}', [ContractorTaskController::class, 'update'])->name('tasks.update');

        Route::get('documents', [ContractorDocumentController::class, 'index'])->name('documents.index');
        Route::post('documents', [ContractorDocumentController::class, 'store'])->name('documents.store');
        Route::get('documents/{document}', [ContractorDocumentController::class, 'show'])->name('documents.show');
        Route::get('documents/{document}/download', [ContractorDocumentController::class, 'download'])->name('documents.download');
        Route::delete('documents/{document}', [ContractorDocumentController::class, 'destroy'])->name('documents.destroy');

        Route::get('change-requests', [ContractorChangeRequestController::class, 'index'])->name('change-requests.index');
        Route::get('change-requests/{changeRequest}', [ContractorChangeRequestController::class, 'show'])->name('change-requests.show');
        Route::post('change-requests/{changeRequest}/respond', [ContractorChangeRequestController::class, 'respond'])->name('change-requests.respond');

        Route::get('messages', [ContractorMessageController::class, 'index'])->name('messages.index');
        Route::get('messages/{conversation}', [ContractorMessageController::class, 'show'])->name('messages.show');

        Route::get('earnings', [ContractorEarningsController::class, 'index'])->name('earnings.index');
        Route::get('earnings/projects/{project}', [ContractorEarningsController::class, 'project'])->name('earnings.project');
        Route::post('earnings/projects/{project}/allocations/{allocation}', [ContractorEarningsController::class, 'pay'])->name('earnings.allocations.pay');
        Route::get('earnings/{earning}', [ContractorEarningsController::class, 'show'])->name('earnings.show');

        Route::get('profile', [ContractorProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [ContractorProfileController::class, 'update'])->name('profile.update');
        Route::post('profile/portfolio', [ContractorProfileController::class, 'storePortfolio'])->name('profile.portfolio.store');
        Route::get('profile/projects/{caseStudy}', [ContractorProfileController::class, 'project'])->name('profile.project');

        Route::get('notifications', [ContractorNotificationController::class, 'index'])->name('notifications.index');
        Route::post('notifications/{notification}/read', [ContractorNotificationController::class, 'read'])->name('notifications.read');
    });
});
Route::post('payments/payhere/notify', PayHereNotifyController::class)->name('payments.payhere.notify');

Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])
    ->name('google.redirect');

Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])
    ->name('google.callback');

Route::view('/privacy-policy', 'marketing.legal', ['page' => 'privacy'])->name('privacy');
Route::view('/terms-of-service', 'marketing.legal', ['page' => 'terms'])->name('terms.public');