<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Seeder;

class PaymentDemoSeeder extends Seeder
{
    /**
     * One pending kitchen milestone keeps Action Required aligned with the home screenshot.
     */
    public function run(): void
    {
        $homeowner = User::query()->where('email', 'homeowner@test.com')->firstOrFail();
        $sequence = 1000;

        foreach ($homeowner->projects as $project) {
            $project->payments()->delete();
        }

        foreach ($homeowner->projects as $project) {
            $project->payments()->delete();
            $quotation = $project->quotations()->where('status', 'approved')->first();
            $paid = $project->status === 'completed' ? (float) $project->current_budget : 400000;

            $fee = round($paid * (Payment::FEE_PERCENT / 100), 2);
            $project->payments()->create([
                'quotation_id' => $quotation?->id,
                'reference' => 'RH-PAY-'.$sequence++,
                'amount' => $paid,
                'renovation_amount' => $paid,
                'platform_fee' => $fee,
                'fee_percent' => Payment::FEE_PERCENT,
                'currency' => 'LKR',
                'method' => 'Bank transfer',
                'status' => Payment::STATUS_PAID,
                'provider_reference' => null,
                'paid_at' => $project->expected_start_date,
                'notes' => 'Recorded inside RenovaHub. PayHere was not called.',
            ]);
        }

        $villa = $homeowner->projects()->where('name', 'Lakeview Villa Renovation')->firstOrFail();
        $villa->payments()->create([
            'quotation_id' => $villa->quotations()->where('status', 'approved')->first()?->id,
            'reference' => 'RH-PAY-'.$sequence,
            'amount' => 150000,
            'renovation_amount' => 150000,
            'platform_fee' => 3000,
            'fee_percent' => Payment::FEE_PERCENT,
            'currency' => 'LKR',
            'method' => null,
            'status' => Payment::STATUS_PENDING,
            'notes' => 'Kitchen installation milestone',
        ]);
    }
}
