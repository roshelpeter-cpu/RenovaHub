<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProfessionalDemoSeeder extends Seeder
{
    public function run(): void
    {
        $designers = [
            $this->professional('designer', 'Sarah Fernando', 'sarah.fernando@renovahub.test', null, 'Interior Designer', 'Calm, light-filled homes', 'Interior designer focused on calm, light-filled homes.', 'Colombo', 'images/renova/about-interior.jpg', 9, 18, 4.8, 250000),
            $this->professional('designer', 'Nethmi Perera', 'nethmi.perera@renovahub.test', null, 'Interior Architect', 'Kitchens and family living', 'Designs practical kitchens and living spaces for family homes.', 'Nugegoda', 'images/renova/feature-collab.jpg', 7, 14, 4.6, 180000),
            $this->professional('designer', 'Ayesha Silva', 'ayesha.silva@renovahub.test', null, 'Interior Designer', 'Tropical villas and bedrooms', 'Works on tropical villas and quiet contemporary bedrooms.', 'Colombo', 'images/renova/feature-green.jpg', 11, 22, 4.9, 300000),
        ];

        $contractors = [
            $this->professional('contractor', 'Kasun Jayawardena', 'abc.renovations@renovahub.test', 'ABC Renovations', 'Renovation contractor', 'Full house and kitchens', 'Residential renovation team working across Colombo.', 'Colombo', 'images/renova/about-exterior.jpg', 14, 40, 4.7, 1500000),
            $this->professional('contractor', 'Dilshan Fernando', 'buildright@renovahub.test', 'BuildRight Lanka', 'Construction lead', 'Extensions and structure', 'Extensions, structural work and full-house renovations.', 'Kotte', 'images/renova/feature-progress.jpg', 16, 36, 4.5, 2000000),
            $this->professional('contractor', 'Ruwan Silva', 'primebuild@renovahub.test', 'PrimeBuild', 'Finishes contractor', 'Kitchens, bathrooms and outdoor', 'Kitchens, bathrooms and outdoor construction.', 'Dehiwala', 'images/renova/feature-docs.jpg', 12, 28, 4.4, 900000),
        ];

        $this->portfolio($designers[0], [
            ['Modern Living Room', 'A light living room with timber, linen and indoor planting.', 'images/renova/about-interior.jpg', 'Living Room', 'Colombo', 1800000, 2400000, 2025],
            ['Minimalist Kitchen', 'Handleless cabinetry and a stone worktop in a compact kitchen.', 'images/renova/feature-plans.jpg', 'Kitchen', 'Nugegoda', 900000, 1400000, 2024],
            ['Contemporary Villa', 'An open villa plan with a shaded courtyard.', 'images/renova/about-exterior.jpg', 'Villa', 'Colombo', 6000000, 9000000, 2023],
            ['Tropical Bedroom', 'A quiet bedroom with warm timber and garden views.', 'images/renova/feature-green.jpg', 'Bedroom', 'Mount Lavinia', 700000, 1100000, 2024],
        ]);
        $this->portfolio($designers[1], [
            ['Family Kitchen', 'A working kitchen planned around storage and daylight.', 'images/renova/feature-tasks.jpg', 'Kitchen', 'Nugegoda', 1100000, 1600000, 2025],
            ['Reading Corner', 'A small living corner with built-in seating.', 'images/renova/feature-collab.jpg', 'Living Room', 'Rajagiriya', 450000, 700000, 2024],
        ]);
        $this->portfolio($designers[2], [
            ['Courtyard Villa', 'Rooms arranged around a planted courtyard.', 'images/renova/feature-progress.jpg', 'Villa', 'Colombo', 5000000, 8000000, 2022],
            ['Garden Bedroom', 'A bedroom opened toward a private garden.', 'images/renova/auth-register.jpg', 'Bedroom', 'Dehiwala', 650000, 950000, 2023],
        ]);
        $this->portfolio($contractors[0], [
            ['Colombo Home Extension', 'A two-storey extension tied back to the original house.', 'images/renova/about-exterior.jpg', 'Extension', 'Colombo', 3500000, 5200000, 2025],
            ['Kitchen Renovation', 'Services relocated and new cabinetry installed.', 'images/renova/feature-docs.jpg', 'Kitchen', 'Bambalapitiya', 1200000, 1800000, 2024],
            ['Bathroom Renovation', 'Waterproofing, tiling and new fittings.', 'images/renova/feature-notes.jpg', 'Bathroom', 'Dehiwala', 600000, 950000, 2024],
            ['Outdoor Patio Construction', 'A shaded patio with drainage and stone paving.', 'images/renova/feature-changes.jpg', 'Outdoor', 'Mount Lavinia', 800000, 1400000, 2023],
        ]);
        $this->portfolio($contractors[1], [
            ['Kotte Extension', 'A ground-floor extension with a new roof line.', 'images/renova/feature-progress.jpg', 'Extension', 'Kotte', 2800000, 4100000, 2024],
            ['Full House Refit', 'Structural repairs followed by interior finishes.', 'images/renovahub-hero.jpg', 'Full House', 'Rajagiriya', 7000000, 9800000, 2023],
        ]);
        $this->portfolio($contractors[2], [
            ['Dehiwala Bathroom', 'A compact bathroom rebuilt with new plumbing.', 'images/renova/feature-quotes.jpg', 'Bathroom', 'Dehiwala', 550000, 850000, 2025],
            ['Patio Build', 'An outdoor seating area with a timber screen.', 'images/renova/feature-green.jpg', 'Outdoor', 'Dehiwala', 700000, 1200000, 2022],
        ]);

        $designers[0]->professionalProfile->reviews()->delete();
        $designers[0]->professionalProfile->reviews()->create([
            'author_name' => 'Maya Jayasinghe',
            'project_title' => 'Townhouse living room',
            'body' => 'Sarah kept the palette calm and the storage actually works for a family.',
            'rating' => 5,
        ]);
        $contractors[0]->professionalProfile->reviews()->delete();
        $contractors[0]->professionalProfile->reviews()->create([
            'author_name' => 'Ishara Fernando',
            'project_title' => 'Colombo extension',
            'body' => 'The site was tidy and the extension matched the old house.',
            'rating' => 4.5,
        ]);
    }

    private function professional(string $role, string $name, string $email, ?string $business, string $title, string $specialization, string $bio, string $location, string $avatar, int $years, int $completed, float $rating, float $price): User
    {
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
                'title' => $title,
                'specialization' => $specialization,
                'bio' => $bio,
                'location' => $location,
                'avatar_path' => $avatar,
                'years_experience' => $years,
                'completed_projects_count' => $completed,
                'rating' => $rating,
                'starting_price' => $price,
            ],
        );

        return $user;
    }

    /**
     * @param  list<array{0: string, 1: string, 2: string, 3: string, 4: string, 5: int, 6: int, 7: int}>  $items
     */
    private function portfolio(User $user, array $items): void
    {
        $profile = $user->professionalProfile()->firstOrFail();
        $profile->portfolioItems()->delete();

        foreach ($items as [$title, $description, $image, $category, $location, $min, $max, $year]) {
            $profile->portfolioItems()->create([
                'title' => $title,
                'description' => $description,
                'image' => $image,
                'category' => $category,
                'location' => $location,
                'budget_min' => $min,
                'budget_max' => $max,
                'completion_year' => $year,
            ]);
        }
    }
}
