<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    /**
     * Demonstration records for the homeowner workspace.
     * Passwords are hashed here and are never rendered in a view.
     */
    public function run(): void
    {
        $homeowner = User::query()->updateOrCreate(
            ['email' => 'homeowner@test.com'],
            [
                'name' => 'Roshel Perera',
                'password' => Hash::make('homeowner@123'),
                'role' => 'homeowner',
                'email_verified_at' => now(),
            ],
        );

        $designers = [
            $this->professional('designer', 'Sarah Fernando', 'sarah.fernando@renovahub.test', null, 'Interior designer focused on calm, light-filled homes.', 'Colombo', 9, 4.8),
            $this->professional('designer', 'Nethmi Perera', 'nethmi.perera@renovahub.test', null, 'Designs practical kitchens and living spaces for family homes.', 'Nugegoda', 7, 4.6),
            $this->professional('designer', 'Ayesha Silva', 'ayesha.silva@renovahub.test', null, 'Works on tropical villas and quiet contemporary bedrooms.', 'Mount Lavinia', 11, 4.9),
        ];

        $contractors = [
            $this->professional('contractor', 'Kasun Jayawardena', 'abc.renovations@renovahub.test', 'ABC Renovations', 'Residential renovation team working across Colombo.', 'Colombo', 14, 4.7),
            $this->professional('contractor', 'Dilshan Fernando', 'buildright@renovahub.test', 'BuildRight Construction', 'Extensions, structural work and full-house renovations.', 'Kotte', 16, 4.5),
            $this->professional('contractor', 'Ruwan Silva', 'primebuild@renovahub.test', 'PrimeBuild Lanka', 'Kitchens, bathrooms and outdoor construction.', 'Dehiwala', 12, 4.4),
        ];

        $this->portfolio($designers[0], [
            ['Modern Living Room', 'A light living room with timber, linen and indoor planting.', 'images/renova/about-interior.jpg', 'Living Room', 2025],
            ['Minimalist Kitchen', 'Handleless cabinetry and a stone worktop in a compact kitchen.', 'images/renova/feature-plans.jpg', 'Kitchen', 2024],
            ['Contemporary Villa', 'An open villa plan with a shaded courtyard.', 'images/renova/about-exterior.jpg', 'Villa', 2023],
            ['Tropical Bedroom', 'A quiet bedroom with warm timber and garden views.', 'images/renova/feature-green.jpg', 'Bedroom', 2024],
        ]);

        $this->portfolio($designers[1], [
            ['Family Kitchen', 'A working kitchen planned around storage and daylight.', 'images/renova/feature-tasks.jpg', 'Kitchen', 2025],
            ['Reading Corner', 'A small living corner with built-in seating.', 'images/renova/feature-collab.jpg', 'Living Room', 2024],
        ]);

        $this->portfolio($designers[2], [
            ['Courtyard Villa', 'Rooms arranged around a planted courtyard.', 'images/renova/feature-progress.jpg', 'Villa', 2022],
            ['Garden Bedroom', 'A bedroom opened toward a private garden.', 'images/renova/auth-register.jpg', 'Bedroom', 2023],
        ]);

        $this->portfolio($contractors[0], [
            ['Colombo Home Extension', 'A two-storey extension tied back to the original house.', 'images/renova/about-exterior.jpg', 'Extension', 2025],
            ['Kitchen Renovation', 'Services relocated and new cabinetry installed.', 'images/renova/feature-docs.jpg', 'Kitchen', 2024],
            ['Bathroom Renovation', 'Waterproofing, tiling and new fittings.', 'images/renova/feature-notes.jpg', 'Bathroom', 2024],
            ['Outdoor Patio Construction', 'A shaded patio with drainage and stone paving.', 'images/renova/feature-changes.jpg', 'Outdoor', 2023],
        ]);

        $this->portfolio($contractors[1], [
            ['Kotte Extension', 'A ground-floor extension with a new roof line.', 'images/renova/feature-progress.jpg', 'Extension', 2024],
            ['Full House Refit', 'Structural repairs followed by interior finishes.', 'images/renovahub-hero.jpg', 'Full House', 2023],
        ]);

        $this->portfolio($contractors[2], [
            ['Dehiwala Bathroom', 'A compact bathroom rebuilt with new plumbing.', 'images/renova/feature-quotes.jpg', 'Bathroom', 2025],
            ['Patio Build', 'An outdoor seating area with a timber screen.', 'images/renova/feature-green.jpg', 'Outdoor', 2022],
        ]);

        $projects = [
            ['Modern Home Renovation', 'A full renovation of a Colombo house, opening the living spaces to the garden and replacing the tired kitchen and bathrooms.', 'full_house', 'house', '42 Flower Road', 'Colombo', 'in_progress', 68, 4500000, '2026-03-02', '2026-11-20', 'images/renovahub-hero.jpg', $designers[0]->id, $contractors[0]->id],
            ['Bathroom Upgrade', 'Replace fittings, waterproof the wet areas and add storage in the family bathroom.', 'bathroom', 'house', '18 Galle Road', 'Dehiwala', 'in_progress', 45, 850000, '2026-04-10', '2026-07-15', 'images/renova/about-interior.jpg', $designers[1]->id, $contractors[2]->id],
            ['Outdoor Extension', 'Add a shaded living extension and patio at the rear of the house.', 'outdoor', 'house', '9 Lake Drive', 'Kotte', 'planning', 12, 2200000, '2026-08-01', '2027-01-30', 'images/renova/feature-green.jpg', null, null],
            ['Kitchen Renovation', 'A completed kitchen refit with new cabinetry, lighting and appliances.', 'kitchen', 'house', '7 High Level Road', 'Nugegoda', 'completed', 100, 1200000, '2025-09-01', '2026-01-18', 'images/renova/feature-plans.jpg', $designers[1]->id, $contractors[0]->id],
            ['Living Room Redesign', 'Replan the living room for seating, storage and better daylight.', 'living_room', 'house', '3 Beach Road', 'Mount Lavinia', 'in_progress', 40, 950000, '2026-02-12', '2026-06-30', 'images/renova/feature-collab.jpg', $designers[2]->id, $contractors[1]->id],
        ];

        $homeowner->projects()->whereNotIn('name', array_column($projects, 0))->delete();

        foreach ($projects as [$name, $description, $renovation, $property, $address, $city, $status, $progress, $budget, $start, $end, $image, $designerId, $contractorId]) {
            $homeowner->projects()->updateOrCreate(
                ['name' => $name],
                [
                    'description' => $description,
                    'renovation_type' => $renovation,
                    'property_type' => $property,
                    'address' => $address,
                    'city' => $city,
                    'province' => 'Sri Lanka',
                    'postal_code' => null,
                    'status' => $status,
                    'progress' => $progress,
                    'estimated_budget' => $budget,
                    'expected_start_date' => $start,
                    'expected_completion_date' => $end,
                    'cover_image' => $image,
                    'designer_id' => $designerId,
                    'contractor_id' => $contractorId,
                ],
            );
        }
    }

    private function professional(
        string $role,
        string $name,
        string $email,
        ?string $business,
        string $bio,
        string $location,
        int $years,
        float $rating,
    ): User {
        $user = User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make('professional-demo-only'),
                'role' => $role,
                'email_verified_at' => now(),
            ],
        );

        $user->professionalProfile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'professional_type' => $role,
                'business_name' => $business,
                'bio' => $bio,
                'location' => $location,
                'years_experience' => $years,
                'rating' => $rating,
            ],
        );

        return $user;
    }

    /**
     * @param  list<array{0: string, 1: string, 2: string, 3: string, 4: int}>  $items
     */
    private function portfolio(User $user, array $items): void
    {
        $profile = $user->professionalProfile()->firstOrFail();
        $profile->portfolioItems()->delete();

        foreach ($items as [$title, $description, $image, $category, $year]) {
            $profile->portfolioItems()->create([
                'title' => $title,
                'description' => $description,
                'image' => $image,
                'category' => $category,
                'completion_year' => $year,
            ]);
        }
    }
}
