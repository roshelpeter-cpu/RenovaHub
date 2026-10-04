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

        $this->change($homeowner, 'Lakeview Villa Renovation', 'Countertop material change', ChangeRequest::STATUS_AWAITING_APPROVAL, 140000, 'Adds about one week', 'material');
        $this->change($homeowner, 'Apartment Interior Makeover', 'Extend the timber screen by one bay', ChangeRequest::STATUS_SUBMITTED, 60000, 'None', 'scope');

        $villa = [
            ['Kitchen countertop material change', ChangeRequest::STATUS_IMPLEMENTED, 180000, 'material'],
            ['Additional storage cabinetry', ChangeRequest::STATUS_APPROVED, 220000, 'scope'],
            ['Bathroom fixture upgrade', ChangeRequest::STATUS_IMPLEMENTED, 95000, 'material'],
            ['Lighting configuration change', ChangeRequest::STATUS_IMPLEMENTED, 40000, 'design'],
            ['Flooring material revision', ChangeRequest::STATUS_REJECTED, 310000, 'material'],
            ['Additional outdoor seating', ChangeRequest::STATUS_IMPLEMENTED, 150000, 'scope'],
            ['Landscape modification', ChangeRequest::STATUS_APPROVED, 120000, 'construction'],
            ['Smart-home integration addition', ChangeRequest::STATUS_IMPLEMENTED, 260000, 'scope'],
        ];
        foreach ($villa as [$title, $status, $cost, $category]) {
            $this->change($homeowner, 'Green Valley Residence', $title, $status, $cost, 'None', $category);
        }

        $family = [
            ['Change the tap finish', ChangeRequest::STATUS_IMPLEMENTED, 18000, 'material'],
            ['Add extra wardrobe lighting', ChangeRequest::STATUS_IMPLEMENTED, 45000, 'design'],
            ['Guest suite door swing reverse', ChangeRequest::STATUS_APPROVED, 22000, 'construction'],
            ['Garden step lighting', ChangeRequest::STATUS_IMPLEMENTED, 38000, 'scope'],
            ['Roof overhang extension', ChangeRequest::STATUS_REJECTED, 190000, 'construction'],
            ['Window film on west elevation', ChangeRequest::STATUS_IMPLEMENTED, 28000, 'material'],
            ['Lounge ceiling fan', ChangeRequest::STATUS_APPROVED, 35000, 'scope'],
            ['Boundary planting revision', ChangeRequest::STATUS_IMPLEMENTED, 42000, 'construction'],
        ];
        foreach ($family as [$title, $status, $cost, $category]) {
            $this->change($homeowner, 'Family Home Extension', $title, $status, $cost, 'None', $category);
        }

        $office = [
            ['Meeting suite glass upgrade', ChangeRequest::STATUS_IMPLEMENTED, 240000, 'material'],
            ['Café counter extension', ChangeRequest::STATUS_APPROVED, 180000, 'scope'],
            ['Additional power on open floor', ChangeRequest::STATUS_IMPLEMENTED, 95000, 'construction'],
            ['Acoustic baffle colour change', ChangeRequest::STATUS_IMPLEMENTED, 22000, 'design'],
            ['Server room cooling upgrade', ChangeRequest::STATUS_APPROVED, 310000, 'scope'],
            ['Reception desk material change', ChangeRequest::STATUS_REJECTED, 140000, 'material'],
            ['Bike store lockers', ChangeRequest::STATUS_IMPLEMENTED, 76000, 'scope'],
            ['After-hours lighting scenes', ChangeRequest::STATUS_IMPLEMENTED, 48000, 'design'],
        ];
        foreach ($office as [$title, $status, $cost, $category]) {
            $this->change($homeowner, 'Commercial Office Renovation', $title, $status, $cost, 'None', $category);
        }
    }

    private function change(User $homeowner, string $projectName, string $title, string $status, ?float $cost, ?string $time, string $category = 'material'): void
    {
        $project = $homeowner->projects()->where('name', $projectName)->firstOrFail();
        $project->changeRequests()->create([
            'requested_by' => $homeowner->id,
            'title' => $title,
            'description' => $title.' on '.$project->name.'.',
            'reason' => 'The homeowner wants the finish to match the rest of the house.',
            'category' => $category,
            'priority' => 'normal',
            'status' => $status,
            'approved_at' => in_array($status, [ChangeRequest::STATUS_APPROVED, ChangeRequest::STATUS_IMPLEMENTED], true) ? now()->subMonths(2) : null,
            'contractor_response' => $cost === null ? null : 'The cost impact is included.',
            'designer_response' => 'The change still fits the approved direction.',
            'cost_impact' => $cost,
            'timeline_impact' => $time,
        ]);
    }
}
