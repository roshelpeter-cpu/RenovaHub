<?php

namespace App\Services\Contractor;

use App\Models\ConstructionAssignment;
use App\Models\ConstructionFirmQuotation;
use App\Models\ProcurementOption;
use App\Models\ProcurementProposal;
use App\Models\Project;
use App\Models\SupplierPrice;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;

/**
 * Contractor comparisons become homeowner decisions.
 * Nothing in this service lets the contractor mark an option approved.
 */
class ProcurementService
{
    public function __construct(
        private NotificationService $notifications,
        private ActivityLogService $activity,
        private FinalDesignService $designs,
    ) {}

    /**
     * @param  list<int>  $priceIds
     */
    public function sendSupplierOptions(User $contractor, Project $project, array $priceIds): ProcurementProposal
    {
        $this->guardDesign($project);

        return DB::transaction(function () use ($contractor, $project, $priceIds) {
            $prices = SupplierPrice::query()
                ->whereIn('id', $priceIds)
                ->whereHas('request', fn ($query) => $query->where('project_id', $project->id)->where('contractor_id', $contractor->id))
                ->get();

            abort_if($prices->count() < 2, 422, 'Select at least two supplier prices.');

            $proposal = $this->open($contractor, $project, ProcurementProposal::TYPE_SUPPLIER);

            foreach ($prices as $price) {
                ProcurementOption::query()->create([
                    'procurement_proposal_id' => $proposal->id,
                    'supplier_price_id' => $price->id,
                ]);
            }

            $this->notifyHomeowner($project, 'Supplier options are ready for approval', 'Choose one supplier price for '.$project->name.'.');

            return $proposal->load('options.price.supplier');
        });
    }

    /**
     * @param  list<int>  $quotationIds
     */
    public function sendFirmOptions(User $contractor, Project $project, array $quotationIds): ProcurementProposal
    {
        $this->guardDesign($project);

        return DB::transaction(function () use ($contractor, $project, $quotationIds) {
            $quotes = ConstructionFirmQuotation::query()
                ->whereIn('id', $quotationIds)
                ->where('project_id', $project->id)
                ->where('contractor_id', $contractor->id)
                ->where('status', ConstructionFirmQuotation::STATUS_RECEIVED)
                ->get();

            abort_if($quotes->count() < 2, 422, 'Select at least two construction firm quotations.');

            $proposal = $this->open($contractor, $project, ProcurementProposal::TYPE_FIRM);

            foreach ($quotes as $quote) {
                $quote->update(['status' => ConstructionFirmQuotation::STATUS_PROPOSED]);
                ProcurementOption::query()->create([
                    'procurement_proposal_id' => $proposal->id,
                    'construction_firm_quotation_id' => $quote->id,
                ]);
            }

            $this->notifyHomeowner($project, 'Construction firm options are ready', 'Choose one construction firm for '.$project->name.'.');

            return $proposal->load('options.quotation.firm');
        });
    }

    /**
     * Homeowner decision. The contractor is rejected here even if they own the proposal.
     */
    public function decide(User $homeowner, ProcurementProposal $proposal, string $decision, ?int $optionId, ?string $note): ProcurementProposal
    {
        abort_unless((int) $proposal->project->user_id === (int) $homeowner->id, 403);
        abort_unless($proposal->status === ProcurementProposal::STATUS_AWAITING, 422);
        abort_unless(in_array($decision, ['approve', 'reject', 'clarification'], true), 422);

        return DB::transaction(function () use ($homeowner, $proposal, $decision, $optionId, $note) {
            if ($decision === 'approve') {
                $option = $proposal->options()->findOrFail($optionId);
                $this->approveOption($proposal, $option);
                $proposal->status = ProcurementProposal::STATUS_APPROVED;
            } elseif ($decision === 'reject') {
                $this->releaseOptions($proposal, ConstructionFirmQuotation::STATUS_REJECTED);
                $proposal->status = ProcurementProposal::STATUS_REJECTED;
            } else {
                $this->releaseOptions($proposal, ConstructionFirmQuotation::STATUS_CLARIFICATION);
                $proposal->status = ProcurementProposal::STATUS_CLARIFICATION;
            }

            $proposal->homeowner_note = $note;
            $proposal->decided_at = now();
            $proposal->save();

            $verb = match ($decision) {
                'approve' => 'approved',
                'reject' => 'rejected',
                default => 'requested clarification on',
            };
            $label = $proposal->type === ProcurementProposal::TYPE_SUPPLIER ? 'supplier' : 'construction firm';
            $this->activity->record($proposal->project, $homeowner, 'procurement.'.$decision, 'Homeowner '.$verb.' the '.$label.' options.');

            return $proposal->fresh();
        });
    }

    /**
     * Assignment is allowed only for the firm the homeowner already approved.
     */
    public function assign(User $contractor, Project $project, ConstructionFirmQuotation $quotation): ConstructionAssignment
    {
        abort_unless((int) $quotation->project_id === (int) $project->id, 404);
        abort_unless((int) $quotation->contractor_id === (int) $contractor->id, 403);
        abort_unless($quotation->status === ConstructionFirmQuotation::STATUS_APPROVED, 422);
        abort_if($project->constructionAssignment()->exists(), 422);

        return ConstructionAssignment::query()->create([
            'project_id' => $project->id,
            'construction_firm_id' => $quotation->construction_firm_id,
            'construction_firm_quotation_id' => $quotation->id,
            'assigned_by' => $contractor->id,
            'assigned_at' => now(),
        ]);
    }

    public function recordFirmQuote(User $contractor, Project $project, array $data): ConstructionFirmQuotation
    {
        $this->guardDesign($project);

        return ConstructionFirmQuotation::query()->create([
            ...$data,
            'project_id' => $project->id,
            'contractor_id' => $contractor->id,
            'status' => ConstructionFirmQuotation::STATUS_RECEIVED,
        ]);
    }

    private function open(User $contractor, Project $project, string $type): ProcurementProposal
    {
        $open = ProcurementProposal::query()
            ->where('project_id', $project->id)
            ->where('type', $type)
            ->where('status', ProcurementProposal::STATUS_AWAITING)
            ->exists();

        abort_if($open, 422, 'A comparison is already waiting for the homeowner.');

        return ProcurementProposal::query()->create([
            'project_id' => $project->id,
            'contractor_id' => $contractor->id,
            'type' => $type,
            'status' => ProcurementProposal::STATUS_AWAITING,
            'submitted_at' => now(),
        ]);
    }

    private function approveOption(ProcurementProposal $proposal, ProcurementOption $option): void
    {
        if ($proposal->type === ProcurementProposal::TYPE_SUPPLIER) {
            $proposal->selected_supplier_price_id = $option->supplier_price_id;
            $option->price?->update(['status' => SupplierPrice::STATUS_ACCEPTED]);

            return;
        }

        $proposal->selected_construction_quotation_id = $option->construction_firm_quotation_id;
        $option->quotation?->update(['status' => ConstructionFirmQuotation::STATUS_APPROVED]);

        ConstructionFirmQuotation::query()
            ->where('project_id', $proposal->project_id)
            ->where('id', '!=', $option->construction_firm_quotation_id)
            ->where('status', ConstructionFirmQuotation::STATUS_PROPOSED)
            ->update(['status' => ConstructionFirmQuotation::STATUS_REJECTED]);
    }

    private function releaseOptions(ProcurementProposal $proposal, string $status): void
    {
        if ($proposal->type !== ProcurementProposal::TYPE_FIRM) {
            return;
        }

        $ids = $proposal->options()->pluck('construction_firm_quotation_id');
        ConstructionFirmQuotation::query()->whereIn('id', $ids)->update(['status' => $status]);
    }

    private function guardDesign(Project $project): void
    {
        abort_if($this->designs->approved($project) === null, 422, 'An approved final design is required before procurement.');
    }

    private function notifyHomeowner(Project $project, string $title, string $body): void
    {
        if ($project->homeowner) {
            $this->notifications->notify($project->homeowner, $title, $body, 'projects', route('homeowner.projects.show', $project));
        }
    }
}
