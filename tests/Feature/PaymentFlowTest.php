<?php

namespace Tests\Feature;

use App\Models\BudgetSubmission;
use App\Models\ContractorEarning;
use App\Models\DesignerEarning;
use App\Models\Payment;
use App\Models\PaymentAllocation;
use App\Models\Project;
use App\Models\ProjectInvitation;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_homeowner_checkout_is_confirmed_only_by_the_payment_result(): void
    {
        [$homeowner, $contractor, $designer, $project, $payment] = $this->budgetPayment();

        $this->actingAs($homeowner)
            ->get(route('homeowner.payments.pay', [$project, $payment]))
            ->assertOk()
            ->assertSee('Project Payment')
            ->assertSee('RenovaHub Service Charge')
            ->assertSee('Confirm Payment');

        $this->assertSame(Payment::STATUS_PENDING, $payment->fresh()->status);

        $this->actingAs($homeowner)
            ->post(route('homeowner.payments.confirm', [$project, $payment]), [
                'token' => 'not-the-signature',
            ])
            ->assertStatus(422);

        $this->assertSame(Payment::STATUS_PENDING, $payment->fresh()->status);

        $this->actingAs($homeowner)
            ->post(route('homeowner.payments.confirm', [$project, $payment]), [
                'token' => app(PaymentService::class)->sandboxToken($payment),
            ])
            ->assertRedirect(route('homeowner.payments.result', [$project, $payment]));

        $payment->refresh();
        $this->assertSame(Payment::STATUS_PAID, $payment->status);
        $this->assertNotNull($payment->paid_at);
        $this->assertSame(10000.0, (float) $payment->allocations()->where('bucket', PaymentAllocation::CONTRACTOR)->value('platform_fee'));
        $this->assertSame(490000.0, (float) $payment->allocations()->where('bucket', PaymentAllocation::CONTRACTOR)->value('net_amount'));
        $this->assertSame(PaymentAllocation::STATUS_PENDING, $payment->allocations()->where('bucket', PaymentAllocation::MATERIALS)->value('status'));
        $this->assertTrue($contractor->contractorEarnings()->where('payment_id', $payment->id)->where('status', ContractorEarning::STATUS_RECORDED)->exists());
        $this->assertSame(5000.0, (float) DesignerEarning::query()->where('designer_id', $designer->id)->where('project_id', $project->id)->value('fee_amount'));

        $this->actingAs($homeowner)
            ->get(route('homeowner.payments.result', [$project, $payment]))
            ->assertOk()
            ->assertSee('Payment Successful')
            ->assertSee('View Receipt')
            ->assertSee('RenovaHub')
            ->assertDontSee('OPay');

        $this->actingAs($contractor)->post(route('homeowner.payments.confirm', [$project, $payment]), [
            'token' => app(PaymentService::class)->sandboxToken($payment),
        ])->assertForbidden();
    }

    public function test_contractor_pays_a_supplier_share_and_cannot_pay_their_own_earning(): void
    {
        [$homeowner, $contractor, , $project, $payment] = $this->budgetPayment();
        app(PaymentService::class)->confirmVerified($payment, 'budget-verified-test');

        $supplier = $payment->allocations()->where('bucket', PaymentAllocation::MATERIALS)->firstOrFail();
        $own = $payment->allocations()->where('bucket', PaymentAllocation::CONTRACTOR)->firstOrFail();
        $this->assertSame(PaymentAllocation::STATUS_PENDING, $supplier->status);

        $this->actingAs($contractor)
            ->get(route('contractor.earnings.project', $project))
            ->assertOk()
            ->assertSee('Supplier Payment')
            ->assertSee('Construction Firm Payment')
            ->assertSee('Contractor Payment')
            ->assertSee('RenovaHub Service Charge')
            ->assertSee('data-receipt-open="allocation-'.$own->id.'"', false);

        $this->actingAs($contractor)
            ->post(route('contractor.earnings.allocations.pay', [$project, $supplier]))
            ->assertRedirect(route('contractor.earnings.project', $project));

        $this->assertSame(PaymentAllocation::STATUS_PAID, $supplier->fresh()->status);
        $this->assertNotNull($supplier->fresh()->paid_at);

        $this->actingAs($contractor)
            ->get(route('contractor.earnings.project', $project))
            ->assertOk()
            ->assertSee('data-receipt-open="allocation-'.$supplier->id.'"', false);

        $this->actingAs($contractor)
            ->post(route('contractor.earnings.allocations.pay', [$project, $supplier]))
            ->assertForbidden();

        $this->actingAs($contractor)
            ->post(route('contractor.earnings.allocations.pay', [$project, $own]))
            ->assertForbidden();

        $other = User::factory()->create(['role' => 'contractor']);
        $this->actingAs($other)
            ->post(route('contractor.earnings.allocations.pay', [$project, $payment->allocations()->where('bucket', PaymentAllocation::CONSTRUCTION)->first()]))
            ->assertForbidden();

        $this->actingAs($homeowner)
            ->get(route('contractor.earnings.index'))
            ->assertForbidden();
    }

    public function test_location_page_does_not_call_for_a_places_api(): void
    {
        $homeowner = User::factory()->create(['role' => 'homeowner']);
        $project = Project::factory()->create([
            'user_id' => $homeowner->id,
            'address' => '42 Flower Road',
            'city' => 'Colombo',
            'province' => 'Western',
            'postal_code' => '00700',
        ]);

        $this->actingAs($homeowner)
            ->get(route('homeowner.projects.location', $project))
            ->assertOk()
            ->assertSee('42 Flower Road')
            ->assertSee('Colombo')
            ->assertDontSee('Google Places')
            ->assertDontSee('googleapis');
    }

    /**
     * @return array{0: User, 1: User, 2: User, 3: Project, 4: Payment}
     */
    private function budgetPayment(): array
    {
        $homeowner = User::factory()->create(['role' => 'homeowner', 'name' => 'Roshel Peter']);
        $contractor = User::factory()->create(['role' => 'contractor', 'name' => 'Kavinda Perera']);
        $designer = User::factory()->create(['role' => 'designer', 'name' => 'Emma Fernando']);
        $project = Project::factory()->create([
            'user_id' => $homeowner->id,
            'name' => 'Apartment Interior Makeover',
            'contractor_id' => $contractor->id,
            'designer_id' => $designer->id,
            'status' => Project::STATUS_IN_PROGRESS,
            'currency' => 'LKR',
        ]);
        $project->invitations()->create([
            'user_id' => $contractor->id,
            'role' => 'contractor',
            'status' => ProjectInvitation::STATUS_ACCEPTED,
            'responded_at' => now(),
        ]);

        $submission = $project->budgetSubmissions()->create([
            'contractor_id' => $contractor->id,
            'designer_fee' => 250000,
            'materials' => 425000,
            'construction' => 1650000,
            'contractor_fee' => 500000,
            'changes' => 0,
            'platform_fee' => 56500,
            'fee_percent' => 2,
            'total' => 2881500,
            'status' => BudgetSubmission::STATUS_APPROVED,
            'submitted_at' => now()->subDay(),
            'decided_at' => now()->subDay(),
        ]);

        $payment = app(PaymentService::class)->openForBudget($submission);
        $submission->update(['payment_id' => $payment->id]);

        return [$homeowner, $contractor, $designer, $project, $payment];
    }
}
