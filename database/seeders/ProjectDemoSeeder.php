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
                        'planning' => ['dates' => 'May 2026 – Jun 2026'],
                        'design' => ['dates' => 'May 2026 – Jun 2026'],
                        'construction' => ['dates' => 'May 2026 – Nov 2026'],
                        'inspection' => ['dates' => 'Not started'],
                    ],
                ],
            ],
            [
                'Apartment Interior Makeover',
                'Interior renovation of a luxury apartment focusing on minimalist layouts, space optimisation and smart-home features.',
                'A compact Colombo apartment is being reworked into an open living kitchen, a calmer bedroom and a guest bath that can also serve visitors. Storage is built into every threshold so the floor stays clear.',
                'living_room', 'apartment', 1450, '21 Flower Road', 'Colombo', '00700', 'in_progress', 35, 18000000, 6200000, '2026-04-01', '2027-02-28', null, 'images/renova/about-interior.jpg', $sithumi, $kavinda, 2,
                ['design' => 100, 'planning' => 70, 'procurement' => 35, 'construction' => 20, 'inspection' => 0],
                [
                    'type_label' => 'Apartment Renovation',
                    'overview' => 'The apartment makeover concentrates on light, storage and a quiet material palette. Joinery is being fabricated off-site while the wet rooms are stripped.',
                    'stage_details' => 'Planning drawings are in review with the building management. Site work has started in the kitchen only.',
                    'activities' => ['Joinery shop drawings', 'Kitchen demolition', 'Electrical re-route', 'Window film and shading'],
                    'stages' => [
                        'planning' => ['dates' => 'Apr 2026 – Jul 2026'],
                        'design' => ['dates' => 'Apr 2026 – Jun 2026'],
                        'construction' => ['dates' => 'Jul 2026 – Jan 2027'],
                        'inspection' => ['dates' => 'Not started'],
                    ],
                ],
            ],
            [
                'Modern Villa Renovation',
                'A complete interior and exterior renovation including living spaces, bedrooms and kitchen improvements.',
                'The older Colombo villa was opened into a single living landscape with a new kitchen, bathrooms and a quieter garden edge.',
                'full_house', 'villa', 2800, '18 Horton Place', 'Colombo', '00700', 'completed', 100, 28500000, 28500000, '2024-06-01', '2025-03-20', '2025-03-18', 'images/renova/feature-collab.jpg', $amaya, $lanka, 4,
                ['design' => 100, 'planning' => 100, 'procurement' => 100, 'construction' => 100, 'inspection' => 100],
                ['type_label' => 'Full Home Renovation', 'overview' => 'Handover is complete. The family now uses the courtyard as the main evening room.'],
            ],
            [
                'Family Home Extension',
                'A ground-floor extension adding a family lounge, guest suite and a covered garden connection in Kandy.',
                'The extension sits beside the original house rather than above it, keeping the roof line calm and the garden continuous.',
                'outdoor', 'house', 2100, '8 Hill Street', 'Kandy', '20000', 'completed', 100, 12000000, 12000000, '2024-04-01', '2025-01-30', '2025-01-22', 'images/renova/feature-green.jpg', $imara, $fine, 1,
                ['design' => 100, 'planning' => 100, 'procurement' => 100, 'construction' => 100, 'inspection' => 100],
                ['type_label' => 'Home Extension', 'overview' => 'The new wing is occupied. Landscaping has matured through one monsoon season.'],
            ],
            [
                'Commercial Office Renovation',
                'A full-floor workplace renovation in Colombo 07 with meeting suites, a staff café and acoustic upgrades.',
                'The floor plate was opened for daylight, with enclosed rooms pulled to the core and a café along the street elevation.',
                'other', 'commercial', 8600, '45 Dharmapala Mawatha', 'Colombo 07', '00700', 'completed', 100, 42000000, 41200000, '2023-11-01', '2024-09-15', '2024-09-12', 'images/renova/feature-progress.jpg', $sithumi, $harbor, 3,
                ['design' => 100, 'planning' => 100, 'procurement' => 100, 'construction' => 100, 'inspection' => 100],
                ['type_label' => 'Commercial Office Renovation', 'overview' => 'The office has been occupied since September 2024. Defects liability is closed.'],
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
                    'designer_id' => $designer->id,
                    'contractor_id' => $contractor->id,
                    'workspace_meta' => $meta,
                ],
            );

            $this->invite($project, $designer, 'designer');
            $this->invite($project, $contractor, 'contractor');
            $this->stages($project, $stages);
            $this->gallery($project, $galleryOffset);
            $this->tasks($project, $designer, $contractor, $status === Project::STATUS_COMPLETED);
            $this->milestones($project, $status === Project::STATUS_COMPLETED, $end);
        }
    }

    /**
     * @return list<string>
     */
    private function photos(): array
    {
        return [
            'images/renova/about-interior.jpg',
            'images/renova/about-exterior.jpg',
            'images/renova/feature-collab.jpg',
            'images/renova/feature-progress.jpg',
            'images/renova/feature-green.jpg',
            'images/renova/feature-docs.jpg',
            'images/renova/feature-plans.jpg',
            'images/renova/feature-tasks.jpg',
            'images/renova/auth-register.jpg',
            'images/renova/hero.jpg',
        ];
    }

    private function gallery(Project $project, int $offset): void
    {
        $project->referenceImages()->delete();
        $photos = $this->photos();

        for ($i = 0; $i < 5; $i++) {
            $path = $photos[($offset + $i) % count($photos)];
            $project->referenceImages()->create([
                'path' => $path,
                'original_name' => basename($path),
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
        foreach ($values as $stage => $percent) {
            $project->progressStages()->create(['stage' => $stage, 'percent' => $percent]);
        }
    }

    private function tasks(Project $project, User $designer, User $contractor, bool $complete): void
    {
        $project->tasks()->delete();
        $rows = $complete
            ? [
                ['Design concept approved', 'design', 'completed', 'high', $designer->id, 100],
                ['Planning package issued', 'planning', 'completed', 'normal', $designer->id, 100],
                ['Construction completed', 'construction', 'completed', 'high', $contractor->id, 100],
                ['Final inspection signed off', 'inspection', 'completed', 'high', $contractor->id, 100],
            ]
            : [
                ['Design concept approved', 'design', 'completed', 'high', $designer->id, 100],
                ['Planning package issued', 'planning', 'completed', 'normal', $designer->id, 100],
                ['Kitchen installation', 'construction', 'in_progress', 'high', $contractor->id, $project->progress],
                ['Final inspection', 'inspection', 'pending', 'normal', $contractor->id, 0],
            ];

        foreach ($rows as [$name, $category, $status, $priority, $assignee, $percent]) {
            $project->tasks()->create([
                'name' => $name,
                'description' => $name.' for '.$project->name.'.',
                'category' => $category,
                'status' => $status,
                'priority' => $priority,
                'assignee_id' => $assignee,
                'progress' => $percent,
                'due_on' => $project->expected_completion_date,
            ]);
        }
    }

    private function milestones(Project $project, bool $complete, string $end): void
    {
        $project->milestones()->delete();
        $project->milestones()->create([
            'title' => $complete ? 'Practical completion' : 'Next site review',
            'description' => $complete ? 'The work was handed over.' : 'Review progress on site with the contractor.',
            'due_on' => $complete ? $project->actual_completion_date : $end,
            'completed_at' => $complete ? $project->actual_completion_date : null,
        ]);
    }
}
