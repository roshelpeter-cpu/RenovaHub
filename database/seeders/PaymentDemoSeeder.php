<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\PaymentAllocation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PaymentDemoSeeder extends Seeder
{
    /**
     * Milestone payments for the homeowner Payments tab.
     * Budget payments on contractor-workspace projects are left untouched.
     */
    public function run(): void
    {
        $homeowner = User::query()->where('email', 'homeowner@test.com')->firstOrFail();
        $projectIds = $homeowner->projects()->pluck('id');

        $records = $this->records();
        $references = array_column($records, 'reference');

        $replaceIds = Payment::query()
            ->whereIn('project_id', $projectIds)
            ->where(function ($query) use ($references) {
                $query->where('reference', 'like', 'RH-PAY-%')
                    ->orWhereIn('reference', $references);
            })
            ->pluck('id');

        PaymentAllocation::query()->whereIn('payment_id', $replaceIds)->delete();
        Payment::query()->whereIn('id', $replaceIds)->delete();

        foreach ($records as $record) {
            $project = $homeowner->projects()
                ->where('name', $record['project'])
                ->where(function ($query) {
                    $query->where('address', '!=', 'Contractor workspace demo')
                        ->orWhereNull('address');
                })
                ->orderBy('id')
                ->firstOrFail();

            $amount = (float) $record['amount'];
            $fee = round($amount * (Payment::FEE_PERCENT / 100), 2);
            $at = Carbon::parse($record['at']);
            $paid = $record['status'] === Payment::STATUS_PAID;

            $payment = $project->payments()->create([
                'payer_id' => $homeowner->id,
                'reference' => $record['reference'],
                'amount' => $amount,
                'renovation_amount' => $amount,
                'platform_fee' => $fee,
                'net_amount' => round($amount + $fee, 2),
                'fee_percent' => Payment::FEE_PERCENT,
                'currency' => 'LKR',
                'method' => $paid ? 'PayHere' : null,
                'provider' => 'payhere',
                'status' => $record['status'],
                'provider_reference' => $paid ? 'sandbox-demo-'.$record['reference'] : null,
                'paid_at' => $paid ? $at : null,
                'notes' => $record['notes'],
            ]);

            $payment->forceFill([
                'created_at' => $at,
                'updated_at' => $at,
            ])->save();
        }
    }

    /**
     * Pending rows stay on open projects so Pay Now can complete checkout.
     *
     * @return list<array{project: string, notes: string, amount: int, status: string, at: string, reference: string}>
     */
    private function records(): array
    {
        return [
            ['project' => 'Lakeview Villa Renovation', 'notes' => 'Design and planning package', 'amount' => 1850000, 'status' => Payment::STATUS_PAID, 'at' => '2025-11-18 10:15:00', 'reference' => 'RH-20251118-1001'],
            ['project' => 'Lakeview Villa Renovation', 'notes' => 'Structural and pool works', 'amount' => 4200000, 'status' => Payment::STATUS_PAID, 'at' => '2026-03-09 11:40:00', 'reference' => 'RH-20260309-1002'],
            ['project' => 'Lakeview Villa Renovation', 'notes' => 'Kitchen installation milestone', 'amount' => 2650000, 'status' => Payment::STATUS_PENDING, 'at' => '2026-09-28 09:20:00', 'reference' => 'RH-20260928-1003'],
            ['project' => 'Lakeview Villa Renovation', 'notes' => 'Smart-home first fix', 'amount' => 740000, 'status' => Payment::STATUS_PENDING, 'at' => '2026-10-04 15:10:00', 'reference' => 'RH-20261004-1004'],
            ['project' => 'Apartment Interior Makeover', 'notes' => 'Joinery deposit', 'amount' => 980000, 'status' => Payment::STATUS_PAID, 'at' => '2026-02-14 09:05:00', 'reference' => 'RH-20260214-1005'],
            ['project' => 'Apartment Interior Makeover', 'notes' => 'Bathroom finishes', 'amount' => 1450000, 'status' => Payment::STATUS_PENDING, 'at' => '2026-10-02 14:35:00', 'reference' => 'RH-20261002-1006'],
            ['project' => 'Green Valley Residence', 'notes' => 'Main construction stage', 'amount' => 2200000, 'status' => Payment::STATUS_PAID, 'at' => '2025-08-22 10:00:00', 'reference' => 'RH-20250822-1007'],
            ['project' => 'Green Valley Residence', 'notes' => 'Landscape and handover', 'amount' => 860000, 'status' => Payment::STATUS_PAID, 'at' => '2025-12-16 16:45:00', 'reference' => 'RH-20251216-1008'],
            ['project' => 'Family Home Extension', 'notes' => 'Foundation and structure', 'amount' => 1750000, 'status' => Payment::STATUS_PAID, 'at' => '2024-08-19 11:20:00', 'reference' => 'RH-20240819-1009'],
            ['project' => 'Family Home Extension', 'notes' => 'Lounge and guest suite', 'amount' => 960000, 'status' => Payment::STATUS_PAID, 'at' => '2025-01-18 13:05:00', 'reference' => 'RH-20250118-1010'],
            ['project' => 'Commercial Office Renovation', 'notes' => 'Fit-out stage one', 'amount' => 3100000, 'status' => Payment::STATUS_PAID, 'at' => '2024-03-12 09:40:00', 'reference' => 'RH-20240312-1011'],
            ['project' => 'Commercial Office Renovation', 'notes' => 'Acoustic and cafe package', 'amount' => 1240000, 'status' => Payment::STATUS_PAID, 'at' => '2024-09-06 15:25:00', 'reference' => 'RH-20240906-1012'],
        ];
    }
}
