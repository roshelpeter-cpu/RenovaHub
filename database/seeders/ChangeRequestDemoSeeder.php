<?php

namespace Database\Seeders;

use App\Models\ChangeRequest;
use App\Models\User;
use Illuminate\Database\Seeder;

class ChangeRequestDemoSeeder extends Seeder
{
    public function run(): void
    {
        $homeowner = User::query()->where('email', 'homeowner@test.com')->firstOrFail();

        foreach ($homeowner->projects as $project) {
            $project->changeRequests()->delete();
        }

        $this->change($homeowner, 'Modern Villa Renovation', 'Countertop material change', ChangeRequest::STATUS_AWAITING_APPROVAL, 140000, 'Adds about one week');
        $this->change($homeowner, 'Luxury Kitchen Transformation', 'Change the tap finish', 'implemented', 18000, 'None');
        $this->change($homeowner, 'Contemporary Bathroom Upgrade', 'Larger mirror', 'implemented', 22000, 'None');
        $this->change($homeowner, 'Modern Family Home Renovation', 'Add extra wardrobe lighting', 'implemented', 45000, 'None');
        $this->change($homeowner, 'Outdoor Living Extension', 'Extend the timber screen by one bay', 'implemented', 60000, 'None');
    }

    private function change(User $homeowner, string $projectName, string $title, string $status, ?float $cost, ?string $time): void
    {
        $project = $homeowner->projects()->where('name', $projectName)->firstOrFail();
        $project->changeRequests()->create([
            'requested_by' => $homeowner->id,
            'title' => $title,
            'description' => $title.' on '.$project->name.'.',
            'reason' => 'The homeowner wants the finish to match the rest of the house.',
            'category' => 'material',
            'priority' => 'normal',
            'status' => $status,
            'contractor_response' => $cost === null ? null : 'The cost impact is included.',
            'designer_response' => 'The change still fits the approved direction.',
            'cost_impact' => $cost,
            'timeline_impact' => $time,
        ]);
    }
}
