<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Database\Seeder;

class QuotationDemoSeeder extends Seeder
{
    /**
     * Pending kitchen quotation is the first Action Required item on Home.
     * Extra pending quotes would hide that item because Home only shows four.
     */
    public function run(): void
    {
        $homeowner = User::query()->where('email', 'homeowner@test.com')->firstOrFail();

        foreach ($homeowner->projects as $project) {
            $project->quotations()->delete();
            $contractorId = $project->contractor_id;

            if ($project->status === Project::STATUS_COMPLETED) {
                $this->quote($project, $contractorId, 'Q-100', 'Final construction quotation', (float) $project->current_budget, Quotation::STATUS_APPROVED);
            }

            if ($project->name === 'Modern Villa Renovation') {
                $this->quote($project, $contractorId, 'Q-241', 'Kitchen renovation quotation', 485000, Quotation::STATUS_PENDING);
                $this->quote($project, $contractorId, 'Q-240', 'Approved living room package', 2100000, Quotation::STATUS_APPROVED);
            }

            if ($project->name === 'Outdoor Living Extension') {
                $this->quote($project, $contractorId, 'Q-310', 'Patio and screen quotation', 1600000, Quotation::STATUS_APPROVED);
            }
        }
    }

    private function quote(Project $project, ?int $contractorId, string $number, string $description, float $total, string $status): void
    {
        $materials = round($total * 0.55, 2);
        $labour = round($total * 0.35, 2);
        $additional = round($total - $materials - $labour, 2);

        $quotation = $project->quotations()->create([
            'contractor_id' => $contractorId,
            'number' => $number,
            'description' => $description,
            'materials' => $materials,
            'labour' => $labour,
            'additional_costs' => $additional,
            'discount' => 0,
            'subtotal' => $total,
            'total' => $total,
            'valid_until' => now()->addMonth(),
            'status' => $status,
        ]);

        $quotation->items()->create([
            'item' => 'Main works',
            'description' => $description,
            'quantity' => 1,
            'unit_cost' => $total,
            'total' => $total,
        ]);
    }
}
