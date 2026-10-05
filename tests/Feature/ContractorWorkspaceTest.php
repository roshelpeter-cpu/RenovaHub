<?php

namespace Tests\Feature;

use App\Models\BudgetSubmission;
use App\Models\ChangeRequest;
use App\Models\ConstructionFirm;
use App\Models\ConstructionFirmQuotation;
use App\Models\DesignConcept;
use App\Models\ContractorEarning;
use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\Document;
use App\Models\Payment;
use App\Models\Project;
use App\Models\ProjectInvitation;
use App\Models\ProjectTask;
use App\Models\Quotation;
use App\Models\Supplier;
use App\Models\SupplierOrder;
use App\Models\SupplierPrice;
use App\Models\SupplierPriceRequest;
use App\Models\User;
use App\Services\Contractor\ProcurementService;
use App\Services\Contractor\SupplierService;
use App\Services\ConversationService;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ContractorWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_contractors_open_the_contractor_dashboard(): void
    {
        $homeowner = User::factory()->create(['role' => 'homeowner']);
        $designer = User::factory()->create(['role' => 'designer']);
        $contractor = User::factory()->create(['role' => 'contractor', 'name' => 'Kavinda Perera']);

        $this->actingAs($homeowner)->get(route('contractor.dashboard'))->assertForbidden();
        $this->actingAs($designer)->get(route('contractor.dashboard'))->assertForbidden();
        $this->actingAs($contractor)->get(route('contractor.dashboard'))->assertOk()->assertSee('Kavinda');
    }

    public function test_contractor_accepts_and_rejects_only_their_invitations(): void
    {
        $homeowner = User::factory()->create(['role' => 'homeowner', 'name' => 'Roshel Peter']);
        $contractor = User::factory()->create(['role' => 'contractor']);
        $other = User::factory()->create(['role' => 'contractor']);

        $pending = $this->offeredProject($homeowner, 'Apartment Interior Makeover');
        $invitation = $pending->invitations()->create([
            'user_id' => $contractor->id,
            'role' => 'contractor',
            'status' => ProjectInvitation::STATUS_PENDING,
        ]);

        $this->actingAs($contractor)->get(route('contractor.invitations.index'))->assertOk()->assertSee('Apartment Interior Makeover');
        $this->actingAs($other)->get(route('contractor.invitations.index'))->assertOk()->assertDontSee('Apartment Interior Makeover');

        $this->actingAs($contractor)->post(route('contractor.invitations.accept', $invitation))
            ->assertRedirect(route('contractor.projects.show', $pending));

        $pending->refresh();
        $this->assertSame($contractor->id, $pending->contractor_id);
        $this->assertSame(ProjectInvitation::STATUS_ACCEPTED, $invitation->fresh()->status);
        $this->actingAs($contractor)->get(route('contractor.projects.index'))->assertSee('Apartment Interior Makeover');
        $this->actingAs($other)->get(route('contractor.projects.show', $pending))->assertNotFound();

        $rejected = $this->offeredProject($homeowner, 'Beach House Construction');
        $decline = $rejected->invitations()->create([
            'user_id' => $contractor->id,
            'role' => 'contractor',
            'status' => ProjectInvitation::STATUS_PENDING,
        ]);

        $this->actingAs($contractor)->post(route('contractor.invitations.reject', $decline))
            ->assertRedirect(route('contractor.invitations.index', ['tab' => 'declined']));

        $this->assertSame(ProjectInvitation::STATUS_DECLINED, $decline->fresh()->status);
        $this->assertNull($rejected->fresh()->contractor_id);
        $this->actingAs($contractor)->get(route('contractor.invitations.index'))->assertDontSee('Beach House Construction');
    }

    public function test_contractor_creates_a_quotation_but_cannot_approve_it(): void
    {
        [$homeowner, $contractor, $project] = $this->assigned();

        $this->actingAs($contractor)->post(route('contractor.quotations.store'), [
            'project_id' => $project->id,
            'description' => 'Kitchen renovation',
            'items' => [
                ['item' => 'Kitchen Cabinets', 'category' => 'materials', 'quantity' => 1, 'unit_cost' => 850000],
                ['item' => 'Labour', 'category' => 'labour', 'quantity' => 1, 'unit_cost' => 300000],
            ],
        ])->assertRedirect();

        $quotation = Quotation::query()->first();
        $this->assertSame(Quotation::STATUS_DRAFT, $quotation->status);
        $this->assertSame('1150000.00', $quotation->total);

        $this->actingAs($contractor)->post(route('contractor.quotations.submit', $quotation))->assertRedirect();
        $this->assertSame(Quotation::STATUS_SUBMITTED, $quotation->fresh()->status);

        $this->actingAs($contractor)->post(route('homeowner.quotations.decide', [$project, $quotation]), [
            'decision' => Quotation::STATUS_APPROVED,
        ])->assertForbidden();

        $this->assertSame(Quotation::STATUS_SUBMITTED, $quotation->fresh()->status);
    }

    public function test_supplier_price_becomes_an_unpaid_order_and_cannot_be_marked_paid(): void
    {
        [$homeowner, $contractor, $project] = $this->assigned();
        $other = User::factory()->create(['role' => 'contractor']);
        $supplier = Supplier::query()->create([
            'name' => 'ABC Interiors',
            'slug' => 'abc-interiors',
            'location' => 'Colombo, Sri Lanka',
            'city' => 'Colombo',
        ]);

        $this->actingAs($contractor)->post(route('contractor.suppliers.request-price.store', $supplier), [
            'project_id' => $project->id,
            'material' => 'Kitchen Cabinets',
            'product' => 'Custom Kitchen Cabinet Set',
            'quantity' => 1,
            'unit' => 'Set',
            'required_by' => now()->addDays(10)->toDateString(),
            'notes' => 'Warm oak finish.',
        ])->assertRedirect();

        $request = SupplierPriceRequest::query()->first();
        $price = app(SupplierService::class)->recordOffer($request, [
            'quoted_price' => 850000,
            'lead_time_days' => 7,
            'delivery' => 'Included',
            'warranty' => '2 years',
            'valid_until' => now()->addDays(14)->toDateString(),
            'message' => 'Delivery included.',
        ]);

        $this->actingAs($contractor)->get(route('contractor.prices.show', $price))->assertOk()->assertSee('LKR 850,000');

        $this->actingAs($contractor)->post(route('contractor.orders.store', $price))->assertRedirect();

        $order = SupplierOrder::query()->first();
        $payment = $order->payment;
        $this->assertSame(SupplierOrder::STATUS_PAYMENT_PENDING, $order->status);
        $this->assertSame(Payment::STATUS_PENDING, $payment->status);
        $this->assertSame('850000.00', $payment->amount);
        $this->assertSame($homeowner->id, $payment->payer_id);

        $this->actingAs($contractor)->put(route('contractor.orders.update', $order), [
            'status' => SupplierOrder::STATUS_PAID,
        ])->assertSessionHasErrors('status');

        $this->assertSame(SupplierOrder::STATUS_PAYMENT_PENDING, $order->fresh()->status);
        $this->assertSame(Payment::STATUS_PENDING, $payment->fresh()->status);
        $this->actingAs($other)->get(route('contractor.orders.show', $order))->assertForbidden();
    }

    public function test_contractor_tasks_documents_changes_messages_and_platform_fee(): void
    {
        [$homeowner, $contractor, $project] = $this->assigned();
        $other = User::factory()->create(['role' => 'contractor']);
        Storage::fake('local');

        $this->actingAs($contractor)->post(route('contractor.tasks.store'), [
            'project_id' => $project->id,
            'name' => 'Kitchen Installation',
            'assignee_label' => 'Construction Team',
            'category' => 'construction',
            'priority' => 'high',
            'status' => 'in_progress',
            'progress' => 40,
            'started_on' => now()->toDateString(),
            'due_on' => now()->addDays(8)->toDateString(),
            'notes' => 'Cabinets on site.',
        ])->assertRedirect(route('contractor.tasks.index'));

        $task = ProjectTask::query()->first();
        $this->assertSame(40, $task->progress);

        $this->actingAs($contractor)->put(route('contractor.tasks.update', $task), [
            'name' => 'Kitchen Installation',
            'assignee_label' => 'Construction Team',
            'priority' => 'high',
            'status' => 'completed',
            'progress' => 40,
            'notes' => 'Finished.',
        ])->assertRedirect();
        $this->assertSame(100, $task->fresh()->progress);

        $this->actingAs($contractor)->post(route('contractor.documents.store'), [
            'project_id' => $project->id,
            'name' => 'Site report',
            'category' => 'construction',
            'file' => UploadedFile::fake()->create('site-report.txt', 8, 'text/plain'),
        ])->assertRedirect();

        $document = Document::query()->first();
        $this->actingAs($contractor)->get(route('contractor.documents.download', $document))->assertOk();
        $this->actingAs($other)->get(route('contractor.documents.download', $document))->assertForbidden();

        $change = $project->changeRequests()->create([
            'requested_by' => $homeowner->id,
            'title' => 'Extend the pool deck',
            'description' => 'Extend the pool deck toward the garden.',
            'category' => 'construction',
            'status' => ChangeRequest::STATUS_SUBMITTED,
        ]);

        $this->actingAs($contractor)->post(route('contractor.change-requests.respond', $change), [
            'cost_impact' => 650000,
            'timeline_impact' => '+ 7 days',
            'feasibility' => 'possible',
            'contractor_response' => 'The deck extension is possible.',
            'design_affected' => 0,
        ])->assertRedirect();

        $this->assertSame('650000.00', $change->fresh()->cost_impact);
        $this->assertSame(ChangeRequest::STATUS_AWAITING_APPROVAL, $change->fresh()->status);

        $conversation = Conversation::query()->create([
            'project_id' => $project->id,
            'kind' => 'professional',
            'last_message_at' => now(),
        ]);
        $conversation->participants()->attach([$contractor->id, $homeowner->id]);
        $own = $conversation->messages()->create(['sender_id' => $contractor->id, 'body' => 'On site tomorrow.']);
        $theirs = $conversation->messages()->create(['sender_id' => $homeowner->id, 'body' => 'Thank you.']);

        $this->assertTrue($contractor->can('delete', $own));
        $this->assertFalse($contractor->can('delete', $theirs));
        app(ConversationService::class)->deleteMessage($contractor, $own);
        $this->assertSoftDeleted($own);
        $this->actingAs($contractor);
        $this->assertNotNull(ConversationMessage::withTrashed()->find($theirs->id));

        $gross = 500000.0;
        $this->assertSame(10000.0, ContractorEarning::feeFor($gross, 2));
        $this->assertSame(490000.0, ContractorEarning::netFor($gross, 2));

        $supplier = Supplier::query()->create(['name' => 'Prime Timber', 'slug' => 'prime-timber', 'location' => 'Moratuwa']);
        $request = SupplierPriceRequest::query()->create([
            'project_id' => $project->id,
            'contractor_id' => $contractor->id,
            'supplier_id' => $supplier->id,
            'material' => 'Flooring',
            'product' => 'Oak Flooring Package',
            'quantity' => 1,
            'unit' => 'package',
            'status' => SupplierPriceRequest::STATUS_QUOTED,
        ]);
        $request->prices()->create([
            'supplier_id' => $supplier->id,
            'quoted_price' => 500000,
            'status' => SupplierPrice::STATUS_OFFERED,
        ]);
        $order = SupplierOrder::query()->create([
            'number' => 'SO-TEST',
            'project_id' => $project->id,
            'contractor_id' => $contractor->id,
            'supplier_id' => $supplier->id,
            'supplier_price_request_id' => $request->id,
            'item' => 'Oak Flooring Package',
            'quantity' => 1,
            'unit' => 'package',
            'amount' => 500000,
            'status' => SupplierOrder::STATUS_PAYMENT_PENDING,
            'ordered_at' => now(),
        ]);
        $payment = app(PaymentService::class)->openForSupplierOrder($order);
        $order->earning()->create([
            'project_id' => $project->id,
            'contractor_id' => $contractor->id,
            'payment_id' => $payment->id,
            'label' => $order->item,
            'gross_amount' => 500000,
            'fee_percent' => $payment->fee_percent,
            'fee_amount' => $payment->platform_fee,
            'net_amount' => ContractorEarning::netFor(500000, (float) $payment->fee_percent),
            'status' => ContractorEarning::STATUS_PENDING,
        ]);

        app(PaymentService::class)->confirmVerified($payment, 'payhere-test-ref');

        $earning = $order->earning()->first();
        $this->assertSame(ContractorEarning::STATUS_RECORDED, $earning->status);
        $this->assertSame('10000.00', $earning->fee_amount);
        $this->assertSame('490000.00', $earning->net_amount);
        $this->assertSame(Payment::STATUS_PAID, $payment->fresh()->status);
        $this->assertSame(SupplierOrder::STATUS_PAID, $order->fresh()->status);
    }

    public function test_homeowner_approves_the_firm_and_the_single_budget_payment_stays_pending(): void
    {
        [$homeowner, $contractor, $project] = $this->assigned();
        $project->budgetItems()->create(['category' => 'Design & Planning', 'amount' => 200000, 'spent_percent' => 0, 'sort_order' => 1]);
        $project->budgetItems()->create(['category' => 'Contractor Fee', 'amount' => 100000, 'spent_percent' => 0, 'sort_order' => 2]);

        DesignConcept::query()->create([
            'project_id' => $project->id,
            'designer_id' => $homeowner->id,
            'title' => 'Approved package',
            'status' => DesignConcept::STATUS_APPROVED,
            'approved_at' => now(),
            'submitted_at' => now(),
        ]);

        $firmA = ConstructionFirm::query()->create(['name' => 'Urban Builders', 'slug' => 'urban-builders', 'city' => 'Colombo']);
        $firmB = ConstructionFirm::query()->create(['name' => 'BuildRight', 'slug' => 'buildright', 'city' => 'Colombo']);
        $procurement = app(ProcurementService::class);
        $procurement->recordFirmQuote($contractor, $project, [
            'construction_firm_id' => $firmA->id,
            'price' => 1650000,
            'duration_days' => 105,
            'start_date' => now()->addDay()->toDateString(),
            'completion_date' => now()->addDays(106)->toDateString(),
            'scope' => 'Interior fit-out',
            'warranty' => '12 months',
        ]);
        $procurement->recordFirmQuote($contractor, $project, [
            'construction_firm_id' => $firmB->id,
            'price' => 1800000,
            'duration_days' => 90,
            'start_date' => now()->addDay()->toDateString(),
            'completion_date' => now()->addDays(91)->toDateString(),
            'scope' => 'Interior fit-out',
            'warranty' => '12 months',
        ]);

        $ids = ConstructionFirmQuotation::query()->where('project_id', $project->id)->pluck('id')->all();
        $proposal = $procurement->sendFirmOptions($contractor, $project, $ids);

        $this->actingAs($contractor)->post(route('homeowner.proposals.decide', $proposal), [
            'decision' => 'approve',
            'option_id' => $proposal->options()->first()->id,
        ])->assertForbidden();

        $this->actingAs($homeowner)->post(route('homeowner.proposals.decide', $proposal), [
            'decision' => 'approve',
            'option_id' => $proposal->options()->first()->id,
        ])->assertRedirect();

        $approved = ConstructionFirmQuotation::query()->where('status', ConstructionFirmQuotation::STATUS_APPROVED)->first();
        $this->actingAs($contractor)->post(route('contractor.firms.assign', [$project, $approved]))->assertRedirect();

        $supplier = Supplier::query()->create(['name' => 'ABC', 'slug' => 'abc-test', 'city' => 'Colombo', 'location' => 'Colombo, Sri Lanka']);
        $request = SupplierPriceRequest::query()->create([
            'project_id' => $project->id,
            'contractor_id' => $contractor->id,
            'supplier_id' => $supplier->id,
            'material' => 'Cabinets',
            'product' => 'Set',
            'quantity' => 1,
            'unit' => 'set',
            'status' => SupplierPriceRequest::STATUS_QUOTED,
        ]);
        $price = $request->prices()->create([
            'supplier_id' => $supplier->id,
            'quoted_price' => 850000,
            'status' => SupplierPrice::STATUS_ACCEPTED,
        ]);
        $supplierProposal = $project->procurementProposals()->create([
            'contractor_id' => $contractor->id,
            'type' => 'supplier',
            'status' => 'approved',
            'selected_supplier_price_id' => $price->id,
            'submitted_at' => now(),
            'decided_at' => now(),
        ]);
        $supplierProposal->options()->create(['supplier_price_id' => $price->id]);

        $this->actingAs($contractor)->post(route('contractor.budget.submit', $project))->assertRedirect();
        $submission = BudgetSubmission::query()->first();
        $this->assertSame(BudgetSubmission::STATUS_AWAITING, $submission->status);
        $this->assertGreaterThan(0, (float) $submission->platform_fee);

        $this->actingAs($contractor)->post(route('homeowner.budget-submissions.decide', $submission), [
            'decision' => 'approve',
        ])->assertForbidden();

        $this->actingAs($homeowner)->post(route('homeowner.budget-submissions.decide', $submission), [
            'decision' => 'approve',
        ])->assertRedirect();

        $payment = $submission->fresh()->payment;
        $this->assertSame(Payment::STATUS_PENDING, $payment->status);
        $this->assertNotNull($payment->budget_submission_id);

        app(PaymentService::class)->confirmVerified($payment, 'budget-verified');
        $this->assertSame(Payment::STATUS_PAID, $payment->fresh()->status);
        $this->assertGreaterThan(0, $payment->allocations()->count());
        $this->assertTrue($contractor->contractorEarnings()->where('payment_id', $payment->id)->where('status', 'recorded')->exists());
    }

    /**
     * @return array{0: User, 1: User, 2: Project}
     */
    private function assigned(): array
    {
        $homeowner = User::factory()->create(['role' => 'homeowner']);
        $contractor = User::factory()->create(['role' => 'contractor']);
        $project = Project::factory()->create([
            'user_id' => $homeowner->id,
            'name' => 'Apartment Interior Makeover',
            'contractor_id' => $contractor->id,
            'status' => Project::STATUS_IN_PROGRESS,
            'address' => '12 Flower Road',
        ]);
        $project->invitations()->create([
            'user_id' => $contractor->id,
            'role' => 'contractor',
            'status' => ProjectInvitation::STATUS_ACCEPTED,
            'responded_at' => now(),
        ]);

        return [$homeowner, $contractor, $project];
    }

    private function offeredProject(User $homeowner, string $name): Project
    {
        return Project::factory()->create([
            'user_id' => $homeowner->id,
            'name' => $name,
            'contractor_id' => null,
            'status' => Project::STATUS_AWAITING_TEAM,
            'description' => 'A renovation offer.',
            'estimated_budget' => 5000000,
        ]);
    }
}
