<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectInvitation;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectDemoSeeder extends Seeder
{
    /**
     * Five owned projects power My Projects: two ongoing, three completed.
     * Gallery photos live on project_reference_images so every card can
     * open a five-image detail view without hardcoding paths in Blade.
     */
    public function run(): void
    {
        $homeowner = User::query()->where('email', 'homeowner@test.com')->firstOrFail();
        $amaya = User::query()->where('email', 'amaya.senarath@renovahub.test')->firstOrFail();
        $dinesh = User::query()->where('email', 'dinesh.jayawardena@renovahub.test')->firstOrFail();
        $sithumi = User::query()->where('email', 'sithumi.fernando@renovahub.test')->firstOrFail();
        $kavinda = User::query()->where('email', 'kavinda.perera@renovahub.test')->firstOrFail();
        $imara = User::query()->where('email', 'imara.perera@renovahub.test')->firstOrFail();
        $lanka = User::query()->where('email', 'lankabuild@renovahub.test')->firstOrFail();
        $harbor = User::query()->where('email', 'harbor.construction@renovahub.test')->firstOrFail();
        $fine = User::query()->where('email', 'finefinish@renovahub.test')->firstOrFail();

        $definitions = [
            [
                'Lakeview Villa Renovation',
                'A complete renovation of a two-storey villa with modern interiors, outdoor deck, swimming pool and landscaping. The project includes structural modifications, interior redesign, kitchen and bathroom upgrades, MEP improvements and smart-home integration.',
                'This project involves the full renovation of a two-storey villa with a focus on modern, functional and elegant living spaces. The renovation includes interior redesign, kitchen and bathroom upgrades, outdoor living area, swimming pool and landscape improvements. Smart-home features and energy-efficient solutions are also integrated to enhance comfort and sustainability.',
                'full_house', 'villa', 3200, '12 Lake Drive', 'Rajagiriya', '10100', 'in_progress', 68, 28500000, 22100000, '2026-01-12', '2026-12-15', null, 'images/renova/about-exterior.jpg', $amaya, $dinesh, 0,
                ['design' => 100, 'planning' => 100, 'procurement' => 80, 'construction' => 68, 'inspection' => 0],
                [
                    'type_label' => 'Full Home Renovation',
                    'overview' => "The villa is being opened toward the lake edge with a new living pavilion, a quieter bedroom wing and a kitchen that can serve both family meals and larger gatherings.\n\nStructural work is limited to selected wall removals and a new deck frame. Interiors use warm stone, oak and forest-green joinery so the house still feels tropical rather than hotel-like.\n\nConstruction is currently focused on wet areas, the pool surround and first-fix electrical so the finishing sequence can start in the third quarter.",
                    'stage_details' => 'Construction is underway on the kitchen, bathrooms, pool deck and primary suite. Design and planning packages are approved and inspections will follow practical completion.',
                    'activities' => ['Kitchen carcass installation', 'Bathroom waterproofing', 'Pool tiling and coping', 'Deck framing and railing', 'Smart-home first fix'],
                    'stages' => [
                        'design' => ['from' => '2026-01-12', 'to' => '2026-02-28'],
                        'planning' => ['from' => '2026-03-01', 'to' => '2026-04-30'],
                        'construction' => ['from' => '2026-05-01', 'to' => '2026-11-30'],
                        'inspection' => ['from' => '2026-12-01', 'to' => '2026-12-15'],
                    ],
                ],
            ],
            [
                'Apartment Interior Makeover',
                'A modern interior makeover for a 3-bedroom apartment focusing on open-plan living, a contemporary kitchen, upgraded bathrooms, custom storage and modern lighting.',
                'This apartment interior makeover focuses on creating a modern, functional living space with better storage, improved lighting and a contemporary design language.',
                'living_room', 'apartment', 1450, '21 Flower Road', 'Colombo 07', '00700', 'in_progress', 45, 12500000, 12500000, '2026-01-05', '2026-06-15', null, 'images/renova/about-interior.jpg', $sithumi, $kavinda, 2,
                ['design' => 100, 'planning' => 100, 'procurement' => 40, 'construction' => 45, 'inspection' => 0],
                [
                    'type_label' => 'Apartment Renovation',
                    'renovation_label' => 'Interior Makeover',
                    'property_label' => 'Apartment',
                    'bedrooms' => 3,
                    'bathrooms' => 2,
                    'progress_note' => 'On track',
                    'overview' => 'This apartment interior makeover focuses on creating a modern, functional living space with better storage, improved lighting and a contemporary design language. The project includes an open-plan living and dining area, a modern kitchen with custom cabinetry, upgraded bathrooms and enhanced interior finishes.',
                    'stages' => [
                        'design' => ['from' => '2026-01-05', 'to' => '2026-02-15', 'notes' => 'Design concepts and initial plans finalised'],
                        'planning' => ['from' => '2026-02-15', 'to' => '2026-03-31', 'notes' => 'Detailed planning and material selection'],
                        'construction' => ['from' => '2026-04-01', 'to' => '2026-05-31', 'notes' => 'Structural work, interior works and installations'],
                        'inspection' => ['from' => '2026-06-01', 'to' => '2026-06-15', 'notes' => 'Quality inspection and project handover'],
                    ],
                ],
            ],
            [
                'Green Valley Residence',
                'A complete renovation of a two-storey family residence, transforming the existing structure into a modern and functional living space with open-plan interiors, upgraded kitchen and bathrooms, custom cabinetry and landscaped outdoor areas.',
                'Green Valley Residence was a full residential renovation focused on modernising the existing property while maintaining its original character.',
                'full_house', 'house', 2800, '18 Horton Place', 'Colombo', '00700', 'completed', 100, 4500000, 4700000, '2025-06-05', '2025-12-18', '2025-12-18', 'images/renova/feature-collab.jpg', $amaya, $lanka, 4,
                ['design' => 100, 'planning' => 100, 'procurement' => 100, 'construction' => 100, 'inspection' => 100],
                [
                    'type_label' => 'Residential Renovation',
                    'renovation_label' => 'Full Renovation',
                    'property_label' => 'Residential',
                    'bedrooms' => 3,
                    'bathrooms' => 2,
                    'status_note' => 'Handover finished',
                    'final_cost' => 4850000,
                    'budget_locked' => true,
                    'overview' => 'Green Valley Residence was a full residential renovation focused on modernising the existing property while maintaining its original character. The project included an open-plan living and dining area, a modern kitchen with custom cabinetry, upgraded bathrooms, new flooring, lighting enhancements, and landscaped outdoor spaces with a swimming pool.',
                    'review' => ['name' => 'Roshel Peter', 'rating' => 5.0, 'body' => 'The house feels new without losing its character. Joinery, stone and the garden edge all landed as drawn.'],
                ],
            ],
            [
                'Family Home Extension',
                'A ground-floor extension adding a family lounge, guest suite and a covered garden connection in Kandy.',
                'The extension sits beside the original house rather than above it, keeping the roof line calm and the garden continuous.',
                'outdoor', 'house', 2100, '8 Hill Street', 'Kandy', '20000', 'completed', 100, 12000000, 12000000, '2024-04-01', '2025-01-30', '2025-01-22', 'images/renova/feature-green.jpg', $imara, $fine, 1,
                ['design' => 100, 'planning' => 100, 'procurement' => 100, 'construction' => 100, 'inspection' => 100],
                ['type_label' => 'Home Extension', 'overview' => 'The new wing is occupied. Landscaping has matured through one monsoon season.', 'review' => ['name' => 'Roshel Peter', 'rating' => 4.8, 'body' => 'The extension sits quietly beside the original house. Guests use the lounge every weekend.']],
            ],
            [
                'Commercial Office Renovation',
                'A full-floor workplace renovation in Colombo 07 with meeting suites, a staff café and acoustic upgrades.',
                'The floor plate was opened for daylight, with enclosed rooms pulled to the core and a café along the street elevation.',
                'other', 'commercial', 8600, '45 Dharmapala Mawatha', 'Colombo 07', '00700', 'completed', 100, 42000000, 41200000, '2023-11-01', '2024-09-15', '2024-09-12', 'images/renova/feature-progress.jpg', $sithumi, $harbor, 3,
                ['design' => 100, 'planning' => 100, 'procurement' => 100, 'construction' => 100, 'inspection' => 100],
                ['type_label' => 'Commercial Office Renovation', 'overview' => 'The office has been occupied since September 2024. Defects liability is closed.', 'review' => ['name' => 'Roshel Peter', 'rating' => 4.7, 'body' => 'Daylight across the floor plate and a café that staff actually use. Acoustics in the meeting suite are excellent.']],
            ],
        ];

        $names = array_column($definitions, 0);
        $homeowner->projects()->whereNotIn('name', $names)->delete();

        foreach ($definitions as $row) {
            [$name, $description, $requirements, $renovation, $property, $size, $address, $city, $postal, $status, $progress, $budget, $current, $start, $end, $finished, $image, $designer, $contractor, $galleryOffset, $stages, $meta] = $row;

            $project = $homeowner->projects()->updateOrCreate(
                ['name' => $name],
                [
                    'description' => $description,
                    'requirements' => $requirements,
                    'renovation_type' => $renovation,
                    'property_type' => $property,
                    'size_sq_ft' => $size,
                    'address' => $address,
                    'city' => $city,
                    'province' => $city === 'Kandy' ? 'Central Province' : 'Western Province',
                    'postal_code' => $postal,
                    'status' => $status,
                    'progress' => $progress,
                    'estimated_budget' => $budget,
                    'current_budget' => $current,
                    'currency' => 'LKR',
                    'expected_start_date' => $start,
                    'expected_completion_date' => $end,
                    'actual_completion_date' => $finished,
                    'cover_image' => $image,
                    'final_cost' => $meta['final_cost'] ?? null,
                    'designer_id' => $designer->id,
                    'contractor_id' => $contractor->id,
                    'workspace_meta' => $meta,
                ],
            );

            $this->invite($project, $designer, 'designer');
            $this->invite($project, $contractor, 'contractor');
            $this->stages($project, $stages);
            $this->gallery($project);
            $this->budget($project);
            $this->tasks($project, $designer, $contractor, $status === Project::STATUS_COMPLETED);
            $this->milestones($project, $status === Project::STATUS_COMPLETED, $end);
        }
    }

    /**
     * @return list<string>
     */
    private function photosFor(Project $project): array
    {
        return match ($project->name) {
            'Lakeview Villa Renovation' => [
                'images/renova/about-exterior.jpg',
                'images/renova/hero.jpg',
                'images/renova/feature-green.jpg',
                'images/renova/about-interior.jpg',
                'images/renova/feature-plans.jpg',
                'images/renova/auth-login.jpg',
            ],
            'Apartment Interior Makeover' => [
                'images/renova/about-interior.jpg',
                'images/renova/feature-collab.jpg',
                'images/renova/auth-register.jpg',
                'images/renova/feature-notes.jpg',
                'images/renova/feature-tasks.jpg',
                'images/renova/feature-plans.jpg',
            ],
            'Green Valley Residence' => [
                'images/renova/feature-collab.jpg',
                'images/renova/feature-quotes.jpg',
                'images/renova/feature-docs.jpg',
                'images/renova/hero.jpg',
                'images/renova/feature-green.jpg',
                'images/renova/about-interior.jpg',
            ],
            'Family Home Extension' => [
                'images/renova/feature-green.jpg',
                'images/renova/feature-changes.jpg',
                'images/renova/about-exterior.jpg',
                'images/renova/feature-plans.jpg',
                'images/renova/feature-progress.jpg',
            ],
            'Commercial Office Renovation' => [
                'images/renova/feature-progress.jpg',
                'images/renova/feature-docs.jpg',
                'images/renova/feature-quotes.jpg',
                'images/renova/feature-tasks.jpg',
                'images/renova/auth-register.jpg',
                'images/renova/feature-notes.jpg',
                'images/renova/feature-collab.jpg',
            ],
            default => [
                'images/renova/about-interior.jpg',
                'images/renova/about-exterior.jpg',
                'images/renova/feature-collab.jpg',
                'images/renova/feature-progress.jpg',
                'images/renova/feature-green.jpg',
            ],
        };
    }

    private function gallery(Project $project): void
    {
        $project->referenceImages()->delete();
        $photos = $this->photosFor($project);
        $project->update(['cover_image' => $photos[0]]);

        foreach ($photos as $path) {
            $project->referenceImages()->create([
                'path' => $path,
                'original_name' => basename($path),
            ]);
        }
    }

    /**
     * Category rows always sum to estimated_budget so the breakdown cannot drift from the tile.
     *
     * @return list<array{0: string, 1: float}>
     */
    private function budgetRows(Project $project): array
    {
        $total = (float) $project->estimated_budget;

        return match ($project->name) {
            'Lakeview Villa Renovation' => [
                ['Design & Consultancy', 2500000],
                ['Structural work', 8000000],
                ['Electrical & plumbing', 3500000],
                ['Materials', 6000000],
                ['Furniture & fixtures', 3000000],
                ['Landscaping', 3500000],
                ['Contingency', 2000000],
            ],
            'Apartment Interior Makeover' => [
                ['Design & Planning', 1500000, 100],
                ['Materials', 4000000, 40],
                ['Construction & Labour', 5500000, 30],
                ['Fixtures & Furniture', 1000000, 0],
                ['Contingency', 500000, 0],
            ],
            'Green Valley Residence' => [
                ['Design & Professional Services', 450000, 100],
                ['Materials', 1650000, 100],
                ['Construction & Labour', 1850000, 100],
                ['Fixtures & Furniture', 500000, 100],
                ['Additional Changes', 250000, 100],
            ],
            'Family Home Extension' => [
                ['Design & Consultancy', 900000],
                ['Construction', 5200000],
                ['Electrical & plumbing', 1600000],
                ['Materials', 2500000],
                ['Furniture', 800000],
                ['Landscaping', 700000],
                ['Contingency', 300000],
            ],
            'Commercial Office Renovation' => [
                ['Architectural / design', 3200000],
                ['Construction', 14000000],
                ['Electrical', 4800000],
                ['HVAC', 5200000],
                ['Furniture', 6100000],
                ['Acoustic treatment', 2700000],
                ['IT infrastructure', 3500000],
                ['Contingency', 2500000],
            ],
            default => [['Works', $total]],
        };
    }

    private function budget(Project $project): void
    {
        $project->budgetItems()->delete();
        $rows = $this->budgetRows($project);
        $assigned = array_sum(array_column($rows, 1));
        $target = (float) $project->estimated_budget;
        $locked = (bool) ($project->workspace_meta['budget_locked'] ?? false);
        if (! $locked && abs($assigned - $target) > 0.5 && $rows !== []) {
            $rows[count($rows) - 1][1] += $target - $assigned;
        }

        foreach ($rows as $order => $row) {
            $project->budgetItems()->create([
                'category' => $row[0],
                'amount' => $row[1],
                'spent_percent' => $row[2] ?? ($project->status === Project::STATUS_COMPLETED ? 100 : 0),
                'sort_order' => $order,
            ]);
        }
    }

    private function invite(Project $project, User $person, string $role): void
    {
        $project->invitations()->where('role', $role)->delete();
        $project->invitations()->create([
            'user_id' => $person->id,
            'role' => $role,
            'status' => ProjectInvitation::STATUS_ACCEPTED,
            'responded_at' => $project->expected_start_date,
        ]);
    }

    /**
     * @param  array<string, int>  $values
     */
    private function stages(Project $project, array $values): void
    {
        $project->progressStages()->delete();
        $dates = $project->workspace_meta['stages'] ?? [];
        foreach ($values as $stage => $percent) {
            $project->progressStages()->create([
                'stage' => $stage,
                'percent' => $percent,
                'started_on' => $dates[$stage]['from'] ?? null,
                'ended_on' => $dates[$stage]['to'] ?? null,
                'notes' => $dates[$stage]['notes'] ?? null,
            ]);
        }
    }

    private function tasks(Project $project, User $designer, User $contractor, bool $complete): void
    {
        $project->tasks()->delete();
        $rows = $this->taskRows($project, $designer, $contractor, $complete);

        foreach ($rows as $row) {
            $project->tasks()->create($row);
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function taskRows(Project $project, User $designer, User $contractor, bool $complete): array
    {
        if ($project->name === 'Apartment Interior Makeover') {
            return [
                $this->task('Install kitchen cabinets', 'Cabinet installation for the open kitchen.', 'construction', 'in_progress', $contractor->id, 60, '2026-04-05', '2026-04-20'),
                $this->task('Interior painting', 'Paint the living, dining and bedroom walls.', 'interior', 'pending', $contractor->id, 0, '2026-10-10', '2026-11-20'),
            ];
        }

        if ($project->name === 'Lakeview Villa Renovation') {
            return [
                $this->task('Pool deck preparation', 'Prepare the pool surround before tiling.', 'construction', 'in_progress', $contractor->id, 40, '2026-05-01', '2026-06-15'),
                $this->task('Outdoor lighting', 'Install landscape and deck lighting.', 'construction', 'pending', $designer->id, 0, '2026-03-01', '2026-04-01'),
            ];
        }

        if ($project->name === 'Green Valley Residence') {
            return [
                $this->task('Finalise architectural drawings', 'Issue the approved drawing set.', 'design', 'completed', $designer->id, 100, '2025-06-01', '2025-06-15'),
                $this->task('Approve kitchen design', 'Sign off the kitchen layout and finishes.', 'design', 'completed', $designer->id, 100, '2025-06-16', '2025-06-30'),
                $this->task('Order flooring materials', 'Confirm timber and tile orders.', 'materials', 'completed', $contractor->id, 100, '2025-07-01', '2025-07-20'),
                $this->task('Complete electrical installation', 'Finish first and second fix electrical.', 'construction', 'completed', $contractor->id, 100, '2025-08-01', '2025-09-15'),
                $this->task('Install kitchen cabinets', 'Fit and adjust the kitchen joinery.', 'construction', 'completed', $contractor->id, 100, '2025-09-20', '2025-10-15'),
                $this->task('Complete painting', 'Apply the final paint system.', 'interior', 'completed', $contractor->id, 100, '2025-10-16', '2025-11-10'),
                $this->task('Final inspection', 'Walk the house and close defects.', 'inspection', 'completed', $contractor->id, 100, '2025-12-01', '2025-12-12'),
                $this->task('Handover', 'Hand the residence to the homeowner.', 'inspection', 'completed', $designer->id, 100, '2025-12-13', '2025-12-18'),
            ];
        }

        $base = $project->expected_start_date?->toDateString() ?? '2024-06-01';
        $end = $project->actual_completion_date?->toDateString() ?? $project->expected_completion_date?->toDateString() ?? '2025-03-01';

        return [
            $this->task('Finalise architectural drawings', 'Approved drawings for '.$project->name.'.', 'design', 'completed', $designer->id, 100, $base, $base),
            $this->task('Approve kitchen design', 'Kitchen sign-off for '.$project->name.'.', 'design', 'completed', $designer->id, 100, $base, $end),
            $this->task('Order flooring materials', 'Material order for '.$project->name.'.', 'procurement', 'completed', $contractor->id, 100, $base, $end),
            $this->task('Complete electrical installation', 'Electrical installation for '.$project->name.'.', 'construction', 'completed', $contractor->id, 100, $base, $end),
            $this->task('Install kitchen cabinets', 'Joinery installation for '.$project->name.'.', 'construction', 'completed', $contractor->id, 100, $base, $end),
            $this->task('Complete painting', 'Decoration for '.$project->name.'.', 'interior', 'completed', $contractor->id, 100, $base, $end),
            $this->task('Final inspection', 'Inspection for '.$project->name.'.', 'inspection', 'completed', $contractor->id, 100, $base, $end),
            $this->task('Handover', 'Handover for '.$project->name.'.', 'inspection', 'completed', $designer->id, 100, $base, $end),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function task(string $name, string $description, string $category, string $status, int $assigneeId, int $progress, string $started, string $due): array
    {
        return [
            'name' => $name,
            'description' => $description,
            'category' => $category,
            'status' => $status,
            'priority' => 'normal',
            'assignee_id' => $assigneeId,
            'progress' => $progress,
            'started_on' => $started,
            'due_on' => $due,
        ];
    }

    private function milestones(Project $project, bool $complete, string $end): void
    {
        $project->milestones()->delete();

        if (! $complete) {
            $project->milestones()->create([
                'title' => 'Next site review',
                'description' => 'Review progress on site with the contractor.',
                'due_on' => $end,
                'completed_at' => null,
            ]);

            return;
        }

        $rows = $project->name === 'Green Valley Residence'
            ? [
                ['Project Created', 'Initial project setup and requirements defined', '2025-06-05'],
                ['Design Completed', 'Design concepts and plans approved', '2025-07-18'],
                ['Planning Completed', 'Detailed planning and material selection finalised', '2025-08-02'],
                ['Procurement Completed', 'All materials and suppliers confirmed', '2025-09-10'],
                ['Construction Completed', 'Construction and installation work finished', '2025-11-28'],
                ['Final Inspection', 'Quality inspection and defect resolution', '2025-12-12'],
                ['Project Completed', 'Handover to homeowner', '2025-12-18'],
            ]
            : $this->spreadMilestones($project);

        foreach ($rows as [$title, $description, $date]) {
            $project->milestones()->create([
                'title' => $title,
                'description' => $description,
                'due_on' => $date,
                'completed_at' => $date,
            ]);
        }
    }

    /**
     * @return list<array{0: string, 1: string, 2: string}>
     */
    private function spreadMilestones(Project $project): array
    {
        $start = $project->expected_start_date ?? now()->subYear();
        $finish = $project->actual_completion_date ?? $project->expected_completion_date ?? now();
        $titles = [
            ['Project Created', 'Initial project setup and requirements defined'],
            ['Design Completed', 'Design concepts and plans approved'],
            ['Planning Completed', 'Detailed planning and material selection finalised'],
            ['Procurement Completed', 'All materials and suppliers confirmed'],
            ['Construction Completed', 'Construction and installation work finished'],
            ['Final Inspection', 'Quality inspection and defect resolution'],
            ['Project Completed', 'Handover to the homeowner'],
        ];
        $days = max(1, $start->diffInDays($finish));

        return collect($titles)->values()->map(function (array $row, int $index) use ($start, $days, $titles) {
            $date = $index === count($titles) - 1
                ? $start->copy()->addDays($days)
                : $start->copy()->addDays((int) floor($days * ($index / (count($titles) - 1))));

            return [$row[0], $row[1], $date->toDateString()];
        })->all();
    }
}
