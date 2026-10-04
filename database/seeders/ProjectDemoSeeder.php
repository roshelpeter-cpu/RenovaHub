<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectInvitation;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectDemoSeeder extends Seeder
{
    public function run(): void
    {
        $homeowner = User::query()->where('email', 'homeowner@test.com')->firstOrFail();
        $roshel = User::query()->where('email', 'roshel.designer@renovahub.test')->firstOrFail();
        $anne = User::query()->where('email', 'anne.fernando@renovahub.test')->firstOrFail();
        $nethmi = User::query()->where('email', 'nethmi.perera@renovahub.test')->firstOrFail();
        $ayesha = User::query()->where('email', 'ayesha.silva@renovahub.test')->firstOrFail();
        $buildright = User::query()->where('email', 'buildright@renovahub.test')->firstOrFail();
        $prime = User::query()->where('email', 'primebuild@renovahub.test')->firstOrFail();

        $definitions = [
            ['Modern Villa Renovation', 'A complete interior and exterior renovation including living spaces, bedrooms and kitchen improvements.', 'Open living, a shaded courtyard and durable finishes for a family that entertains.', 'full_house', 'villa', '18 Horton Place', 'Colombo', '00700', 'in_progress', 68, 5000000, 4650000, '2026-03-02', '2026-12-15', null, 'images/renovahub-hero.jpg', $roshel, $anne],
            ['Luxury Kitchen Transformation', 'Modern kitchen transformation with custom cabinetry, premium finishes and energy-efficient appliances.', 'Large island, extra storage and energy-efficient lighting.', 'kitchen', 'house', '27 High Level Road', 'Nugegoda', '10250', 'completed', 100, 2200000, 2140000, '2025-11-03', '2026-02-27', '2026-02-20', 'images/renova/feature-plans.jpg', $nethmi, $prime],
            ['Contemporary Bathroom Upgrade', 'Complete bathroom upgrade with modern fixtures, walk-in shower, neutral tones and smart storage solutions.', 'Walk-in shower, quieter ventilation and a calmer tile palette.', 'bathroom', 'house', '14 Galle Road', 'Dehiwala', '10350', 'completed', 100, 980000, 960000, '2025-08-11', '2025-11-28', '2025-11-21', 'images/renova/about-interior.jpg', $ayesha, $buildright],
            ['Modern Family Home Renovation', 'Full house renovation including living spaces, kitchen, bedrooms and outdoor area with modern tropical design.', 'Open the kitchen to the dining room, add storage, and keep the garden connection.', 'full_house', 'house', '9 Lake Drive', 'Kotte', '10100', 'completed', 100, 2500000, 2480000, '2025-06-01', '2026-01-20', '2026-01-15', 'images/renova/feature-collab.jpg', $nethmi, $prime],
            ['Outdoor Living Extension', 'Adding an outdoor living space with deck, seating area and landscaping for a seamless indoor-outdoor experience.', 'Covered seating, drainage and a timber screen toward the lane.', 'outdoor', 'house', '3 Beach Road', 'Mount Lavinia', '10370', 'in_progress', 35, 1820000, 1600000, '2026-06-01', '2027-03-31', null, 'images/renova/feature-green.jpg', $nethmi, $prime],
        ];

        $names = array_column($definitions, 0);
        $homeowner->projects()->whereNotIn('name', $names)->delete();

        foreach ($definitions as [$name, $description, $requirements, $renovation, $property, $address, $city, $postal, $status, $progress, $budget, $current, $start, $end, $finished, $image, $designer, $contractor]) {
            $project = $homeowner->projects()->updateOrCreate(
                ['name' => $name],
                [
                    'description' => $description,
                    'requirements' => $requirements,
                    'renovation_type' => $renovation,
                    'property_type' => $property,
                    'address' => $address,
                    'city' => $city,
                    'province' => 'Western Province',
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
                ],
            );

            $this->invite($project, $designer, 'designer');
            $this->invite($project, $contractor, 'contractor');
            $this->stages($project, $status === Project::STATUS_COMPLETED);
            $this->tasks($project, $designer, $contractor, $status === Project::STATUS_COMPLETED);
            $this->milestones($project, $status === Project::STATUS_COMPLETED, $end);
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

    private function stages(Project $project, bool $complete): void
    {
        $project->progressStages()->delete();
        $values = $complete
            ? ['design' => 100, 'planning' => 100, 'procurement' => 100, 'construction' => 100, 'inspection' => 100]
            : ($project->name === 'Modern Villa Renovation'
                ? ['design' => 100, 'planning' => 100, 'procurement' => 75, 'construction' => 45, 'inspection' => 0]
                : ['design' => 80, 'planning' => 60, 'procurement' => 20, 'construction' => 10, 'inspection' => 0]);

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
                ['Materials ordered', 'procurement', 'completed', 'normal', $contractor->id, 100],
                ['Construction completed', 'construction', 'completed', 'high', $contractor->id, 100],
                ['Final inspection signed off', 'inspection', 'completed', 'high', $contractor->id, 100],
            ]
            : [
                ['Design concept approved', 'design', 'completed', 'high', $designer->id, 100],
                ['Planning package issued', 'planning', 'completed', 'normal', $designer->id, 100],
                ['Material selection completed', 'procurement', 'completed', 'normal', $designer->id, 100],
                ['Kitchen installation', 'construction', 'in_progress', 'high', $contractor->id, 45],
                ['Electrical first fix', 'construction', 'pending', 'normal', $contractor->id, 0],
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
