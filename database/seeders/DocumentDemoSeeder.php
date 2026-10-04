<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DocumentDemoSeeder extends Seeder
{
    public function run(): void
    {
        $homeowner = User::query()->where('email', 'homeowner@test.com')->firstOrFail();
        $source = public_path('images/renova/feature-docs.jpg');
        $bytes = is_file($source) ? file_get_contents($source) : 'demo';

        foreach ($homeowner->projects as $project) {
            $project->documents()->delete();

            foreach ($this->rowsFor($project->name) as $row) {
                $slug = str($row['name'])->slug();
                $path = 'projects/'.$project->id.'/documents/'.$slug.'.jpg';
                Storage::disk('local')->put($path, $bytes);
                $document = $project->documents()->create([
                    'uploaded_by' => $row['by'] === 'contractor' ? $project->contractor_id : $project->designer_id,
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'original_name' => $slug.'.jpg',
                    'category' => $row['category'],
                    'disk' => 'local',
                    'path' => $path,
                    'mime' => 'image/jpeg',
                    'size' => $row['size'],
                ]);
                $document->forceFill([
                    'created_at' => $row['date'],
                    'updated_at' => $row['date'],
                ])->save();
            }
        }
    }

    /**
     * @return list<array{name: string, description: string, category: string, by: string, size: int, date: string}>
     */
    private function rowsFor(string $project): array
    {
        if ($project === 'Apartment Interior Makeover') {
            return [
                $this->row('Design Proposal', 'Approved design proposal and concept drawings.', 'design', 'designer', 4404019, '2026-01-10'),
                $this->row('Initial Quotation', 'First contractor quotation for the interior package.', 'quotation', 'contractor', 1258291, '2026-01-18'),
            ];
        }

        if ($project === 'Lakeview Villa Renovation') {
            return [
                $this->row('Site Photos', 'Current site photographs of the villa.', 'site_photos', 'contractor', 3145728, '2026-05-12'),
                $this->row('Material Specifications', 'Approved finishes for the kitchen and pool deck.', 'materials', 'designer', 2097152, '2026-04-02'),
            ];
        }

        if ($project === 'Green Valley Residence') {
            return [
                $this->row('Final Contract', 'Signed final contract for project execution.', 'contracts', 'contractor', 2516582, '2025-06-05'),
                $this->row('Approved Design Package', 'Approved drawings, elevations and finishes.', 'design', 'designer', 4404019, '2025-07-18'),
                $this->row('Final Quotation', 'Approved quotation for the completed scope.', 'quotation', 'contractor', 1572864, '2025-07-22'),
                $this->row('Final Invoice', 'Closing invoice for the residence.', 'invoices', 'contractor', 980000, '2025-12-16'),
                $this->row('Material Receipts', 'Receipts for flooring, stone and joinery.', 'materials', 'contractor', 734003, '2025-09-12'),
                $this->row('Inspection Report', 'Final inspection notes and defect list.', 'inspection', 'contractor', 640000, '2025-12-12'),
                $this->row('Completion Certificate', 'Practical completion certificate.', 'inspection', 'designer', 420000, '2025-12-18'),
                $this->row('Warranty Documents', 'Workmanship and appliance warranties.', 'warranty', 'contractor', 880000, '2025-12-18'),
            ];
        }

        $prefix = $project === 'Family Home Extension' ? 'Extension' : 'Office';

        return [
            $this->row($prefix.' Contract', 'Signed contract for '.$project.'.', 'contracts', 'contractor', 1800000, '2024-06-01'),
            $this->row($prefix.' Design Package', 'Approved design package.', 'design', 'designer', 2400000, '2024-07-01'),
            $this->row($prefix.' Quotation', 'Approved quotation.', 'quotation', 'contractor', 900000, '2024-07-15'),
            $this->row($prefix.' Invoice', 'Final invoice.', 'invoices', 'contractor', 700000, '2025-01-10'),
            $this->row($prefix.' Material Schedule', 'Material schedule and receipts.', 'materials', 'contractor', 650000, '2024-08-01'),
            $this->row($prefix.' Inspection Report', 'Inspection report.', 'inspection', 'contractor', 500000, '2025-01-12'),
            $this->row($prefix.' Completion Certificate', 'Completion certificate.', 'warranty', 'designer', 400000, '2025-01-20'),
            $this->row($prefix.' Technical Drawings', 'Construction drawings.', 'technical', 'designer', 3200000, '2024-06-20'),
        ];
    }

    /**
     * @return array{name: string, description: string, category: string, by: string, size: int, date: string}
     */
    private function row(string $name, string $description, string $category, string $by, int $size, string $date): array
    {
        return compact('name', 'description', 'category', 'by', 'size', 'date');
    }
}
