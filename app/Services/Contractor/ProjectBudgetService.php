<?php

namespace App\Services\Contractor;

use App\Models\BudgetSubmission;
use App\Models\ChangeRequest;
use App\Models\ConstructionFirmQuotation;
use App\Models\ContractorEarning;
use App\Models\Payment;
use App\Models\ProcurementProposal;
use App\Models\Project;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\PaymentService;
use Illuminate\Support\Facades\DB;

/**
 * Builds the one project budget from approved design procurement.
 * Sending it does not approve it, and it does not mark a payment as paid.
 */
class ProjectBudgetService
{
    public function __construct(
        private FinalDesignService $designs,
        private PaymentService $payments,
        private NotificationService $notifications,
    ) {}

    /**
     * @return array{designer_fee: float, materials: float, construction: float, contractor_fee: float, changes: float, platform_fee: float, fee_percent: float, subtotal: float, total: float}
     */
    public function preview(Project $project): array
    {
        $designer = (float) ($project->budgetItems->firstWhere('category', 'Design & Planning')?->amount ?? 0);
        $contractorFee = (float) ($project->budgetItems->firstWhere('category', 'Contractor Fee')?->amount ?? 0);
        $materials = (float) $project->procurementProposals()
            ->where('type', ProcurementProposal::TYPE_SUPPLIER)
            ->where('status', ProcurementProposal::STATUS_APPROVED)
            ->with('selectedPrice')
            ->get()
            ->sum(fn (ProcurementProposal $proposal) => (float) ($proposal->selectedPrice?->quoted_price ?? 0));
        $construction = (float) ($project->constructionAssignment?->quotation?->price
            ?? ConstructionFirmQuotation::query()
                ->where('project_id', $project->id)
                ->where('status', ConstructionFirmQuotation::STATUS_APPROVED)
                ->value('price')
            ?? 0);
        $changes = (float) $project->changeRequests()
            ->whereIn('status', [ChangeRequest::STATUS_APPROVED, ChangeRequest::STATUS_IMPLEMENTED])
            ->sum('cost_impact');

        $subtotal = round($designer + $materials + $construction + $contractorFee + $changes, 2);
        $percent = (float) config('renovahub.platform_fee_percent', 2);
        $fee = round($subtotal * ($percent / 100), 2);

        return [
            'designer_fee' => $designer,
            'materials' => $materials,
            'construction' => $construction,
            'contractor_fee' => $contractorFee,
            'changes' => $changes,
            'platform_fee' => $fee,
            'fee_percent' => $percent,
            'subtotal' => $subtotal,
            'total' => round($subtotal + $fee, 2),
        ];
    }

    public function submit(User $contractor, Project $project): BudgetSubmission
    {
        abort_if($this->designs->approved($project) === null, 422);
        abort_unless(
            $project->procurementProposals()->where('type', ProcurementProposal::TYPE_SUPPLIER)->where('status', ProcurementProposal::STATUS_APPROVED)->exists(),
            422,
        );
        abort_unless(
            $project->constructionAssignment()->exists()
            || ConstructionFirmQuotation::query()->where('project_id', $project->id)->where('status', ConstructionFirmQuotation::STATUS_APPROVED)->exists(),
            422,
        );

        $figures = $this->preview($project);
        abort_if($figures['total'] <= 0, 422);

        $existing = $project->budgetSubmissions()->where('status', BudgetSubmission::STATUS_AWAITING)->first();
        abort_if($existing !== null, 422);

        $submission = $project->budgetSubmissions()->create([
            'contractor_id' => $contractor->id,
            ...collect($figures)->except('subtotal')->all(),
            'status' => BudgetSubmission::STATUS_AWAITING,
            'submitted_at' => now(),
        ]);

        if ($project->homeowner) {
            $this->notifications->notify(
                $project->homeowner,
                'Final budget is ready',
                'Review the single project budget for '.$project->name.'.',
                'payments',
                route('homeowner.projects.show', $project),
            );
        }

        return $submission;
    }

    /**
     * Homeowner approval opens one pending payment. PayHere is not called.
     */
    public function decide(User $homeowner, BudgetSubmission $submission, string $decision): BudgetSubmission
    {
        abort_unless((int) $submission->project->user_id === (int) $homeowner->id, 403);
        abort_unless($homeowner->isHomeowner(), 403);
        abort_unless($submission->status === BudgetSubmission::STATUS_AWAITING, 422);
        abort_unless(in_array($decision, ['approve', 'reject'], true), 422);

        return DB::transaction(function () use ($submission, $decision) {
            if ($decision === 'reject') {
                $submission->update([
                    'status' => BudgetSubmission::STATUS_REJECTED,
                    'decided_at' => now(),
                ]);

                return $submission->fresh();
            }

            $payment = $this->payments->openForBudget($submission);

            $submission->update([
                'status' => BudgetSubmission::STATUS_APPROVED,
                'payment_id' => $payment->id,
                'decided_at' => now(),
            ]);

            return $submission->fresh('payment');
        });
    }

    public function recordEarning(Payment $payment): void
    {
        $submission = $payment->budgetSubmission;

        if ($submission === null || $payment->status !== Payment::STATUS_PAID) {
            return;
        }

        $percent = (float) $submission->fee_percent;
        $gross = (float) $submission->contractor_fee;
        $fee = ContractorEarning::feeFor($gross, $percent);

        ContractorEarning::query()->updateOrCreate(
            ['payment_id' => $payment->id, 'contractor_id' => $submission->contractor_id],
            [
                'project_id' => $submission->project_id,
                'label' => 'Contractor fee · '.$submission->project->name,
                'gross_amount' => $gross,
                'fee_percent' => $percent,
                'fee_amount' => $fee,
                'net_amount' => ContractorEarning::netFor($gross, $percent),
                'status' => ContractorEarning::STATUS_RECORDED,
                'reference' => $payment->reference,
                'recorded_on' => now()->toDateString(),
                'notes' => 'RenovaHub platform service fee on the contractor portion of the verified project payment.',
            ],
        );
    }
}
