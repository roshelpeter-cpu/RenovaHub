<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Database\Seeder;

class QuotationDemoSeeder extends Seeder
{
    /**
     * Lakeview keeps a single pending kitchen quote so Home Action Required
     * still points at a real row. Completed projects carry a historical set.
     */
    public function run(): void
    {
        $homeowner = User::query()->where('email', 'homeowner@test.com')->firstOrFail();

        foreach ($homeowner->projects as $project) {
            $project->quotations()->delete();
            $contractorId = $project->contractor_id;

            if ($project->name === 'Lakeview Villa Renovation') {
                $this->quote($project, $contractorId, 'Q-241', 'Kitchen renovation quotation', 'Kitchen', 485000, Quotation::STATUS_PENDING, null);
            } elseif ($project->name === 'Apartment Interior Makeover') {
                $this->quote($project, $contractorId, 'Q-310', 'Joinery and lighting package', 'Interior', 1600000, Quotation::STATUS_APPROVED, 'Approved', '2026-01-18', '2026-01-24');
            } elseif ($project->name === 'Green Valley Residence') {
                $this->quote($project, $contractorId, 'Q-501', 'Initial construction quotation', 'Construction', 1500000, Quotation::STATUS_APPROVED, 'Approved', '2025-06-08', '2025-06-12');
                $this->quote($project, $contractorId, 'Q-502', 'Electrical installation quotation', 'Electrical', 620000, Quotation::STATUS_APPROVED, 'Approved', '2025-06-20', '2025-06-28');
                $this->quote($project, $contractorId, 'Q-503', 'Kitchen and joinery quotation', 'Kitchen', 780000, Quotation::STATUS_APPROVED, 'Approved', '2025-07-02', '2025-07-10');
                $this->quote($project, $contractorId, 'Q-504', 'Flooring quotation', 'Materials', 410000, Quotation::STATUS_APPROVED, 'Approved', '2025-07-18', '2025-07-24');
                $this->quote($project, $contractorId, 'Q-505', 'Bathroom fittings quotation', 'Bathrooms', 360000, Quotation::STATUS_APPROVED, 'Approved', '2025-08-01', '2025-08-08');
                $this->quote($project, $contractorId, 'Q-506', 'Painting quotation', 'Interior', 240000, Quotation::STATUS_APPROVED, 'Approved', '2025-08-15', '2025-08-20');
                $this->quote($project, $contractorId, 'Q-507', 'Landscape quotation', 'Landscape', 290000, Quotation::STATUS_APPROVED, 'Approved', '2025-09-02', '2025-09-09');
                $this->quote($project, $contractorId, 'Q-508', 'Final variations quotation', 'Variations', 150000, Quotation::STATUS_APPROVED, 'Approved', '2025-11-01', '2025-11-06');
            } elseif ($project->name === 'Family Home Extension') {
                $this->quote($project, $contractorId, 'Q-601', 'Extension structure quotation', 'Construction', 5200000, Quotation::STATUS_APPROVED, 'Approved', '2024-04-08', '2024-04-15');
                $this->quote($project, $contractorId, 'Q-602', 'Electrical and plumbing quotation', 'MEP', 1600000, Quotation::STATUS_APPROVED, 'Approved', '2024-05-02', '2024-05-10');
                $this->quote($project, $contractorId, 'Q-603', 'Joinery quotation', 'Joinery', 2100000, Quotation::STATUS_APPROVED, 'Approved', '2024-06-01', '2024-06-08');
                $this->quote($project, $contractorId, 'Q-604', 'Landscape quotation', 'Landscape', 700000, Quotation::STATUS_APPROVED, 'Approved', '2024-07-01', '2024-07-06');
                $this->quote($project, $contractorId, 'Q-605', 'Guest suite finishes', 'Interior', 480000, Quotation::STATUS_APPROVED, 'Approved', '2024-08-01', '2024-08-08');
                $this->quote($project, $contractorId, 'Q-606', 'Roofing quotation', 'Construction', 920000, Quotation::STATUS_APPROVED, 'Approved', '2024-04-20', '2024-04-28');
                $this->quote($project, $contractorId, 'Q-607', 'Window package', 'Materials', 540000, Quotation::STATUS_APPROVED, 'Approved', '2024-09-01', '2024-09-05');
            } elseif ($project->name === 'Commercial Office Renovation') {
                $this->quote($project, $contractorId, 'Q-701', 'Base build quotation', 'Construction', 14000000, Quotation::STATUS_APPROVED, 'Approved', '2023-11-08', '2023-11-20');
                $this->quote($project, $contractorId, 'Q-702', 'Electrical quotation', 'Electrical', 4800000, Quotation::STATUS_APPROVED, 'Approved', '2023-12-01', '2023-12-10');
                $this->quote($project, $contractorId, 'Q-703', 'HVAC quotation', 'HVAC', 5200000, Quotation::STATUS_APPROVED, 'Approved', '2024-01-08', '2024-01-15');
                $this->quote($project, $contractorId, 'Q-704', 'Furniture quotation', 'Furniture', 6100000, Quotation::STATUS_APPROVED, 'Approved', '2024-02-01', '2024-02-12');
                $this->quote($project, $contractorId, 'Q-705', 'Acoustic and IT package', 'Fit-out', 6200000, Quotation::STATUS_APPROVED, 'Approved', '2024-03-01', '2024-03-08');
                $this->quote($project, $contractorId, 'Q-706', 'Café fit-out quotation', 'Interior', 1800000, Quotation::STATUS_APPROVED, 'Approved', '2024-03-20', '2024-03-28');
                $this->quote($project, $contractorId, 'Q-707', 'Meeting suite glazing', 'Materials', 960000, Quotation::STATUS_APPROVED, 'Approved', '2024-04-02', '2024-04-09');
            }
        }
    }

    private function quote(Project $project, ?int $contractorId, string $number, string $description, string $category, float $total, string $status, ?string $label, ?string $submitted = null, ?string $approved = null): void
    {
        $materials = round($total * 0.55, 2);
        $labour = round($total * 0.35, 2);
        $additional = round($total - $materials - $labour, 2);

        $quotation = $project->quotations()->create([
            'contractor_id' => $contractorId,
            'number' => $number,
            'description' => $description,
            'category' => $category,
            'materials' => $materials,
            'labour' => $labour,
            'additional_costs' => $additional,
            'discount' => 0,
            'subtotal' => $total,
            'total' => $total,
            'valid_until' => now()->addMonth(),
            'status' => $status,
            'notes' => $label,
            'approved_at' => $status === Quotation::STATUS_APPROVED ? $approved : null,
        ]);

        if ($submitted !== null) {
            $quotation->forceFill([
                'created_at' => $submitted,
                'updated_at' => $approved ?? $submitted,
            ])->save();
        }

        $quotation->items()->create([
            'item' => $category,
            'description' => $description,
            'quantity' => 1,
            'unit_cost' => $total,
            'total' => $total,
        ]);
    }
}
