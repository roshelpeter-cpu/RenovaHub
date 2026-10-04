<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ExploreProfessionalSeeder extends Seeder
{
    /**
     * Public directory listings only. Team-picker profiles stay unlisted
     * so Explore can match the assignment counts without duplicating wizard data.
     */
    public function run(): void
    {
        $images = [
            'images/renova/about-interior.jpg',
            'images/renova/feature-collab.jpg',
            'images/renova/auth-register.jpg',
            'images/renova/feature-progress.jpg',
            'images/renova/about-exterior.jpg',
            'images/renova/feature-green.jpg',
            'images/renova/feature-docs.jpg',
            'images/renova/feature-plans.jpg',
            'images/renova/feature-tasks.jpg',
        ];

        $designers = [
            ['Amaya Senarath', 'amaya.senarath@renovahub.test', 'Interior Designer', ['Modern', 'Minimal', 'Contemporary'], 4.8, 42, true, true, 'I specialise in residential interiors, space planning and creating personalised designs.', 6, 50, 180000],
            ['Dinesh Jayawardena', 'dinesh.jayawardena@renovahub.test', 'Interior Designer', ['Modern', 'Luxury', 'Residential'], 4.9, 18, true, true, 'Luxury residential interiors with a calm, considered material palette.', 8, 22, 320000],
            ['Sithumi Fernando', 'sithumi.fernando@renovahub.test', 'Interior Designer', ['Scandinavian', 'Modern', 'Commercial'], 4.9, 31, true, true, 'Scandinavian-led commercial and home interiors across Colombo.', 9, 40, 280000],
            ['Kavinda Perera', 'kavinda.perera@renovahub.test', 'Interior Designer', ['Industrial', 'Modern', 'Minimal'], 4.6, 12, true, true, 'Industrial and minimal interiors for compact city homes.', 5, 16, 150000],
            ['Nethmi Wijesinghe', 'nethmi.wijesinghe@renovahub.test', 'Interior Designer', ['Modern', 'Minimal', 'Residential'], 4.8, 28, false, true, 'Interior designer with a focus on modern and functional living spaces. Specialises in residential renovations and space planning.', 7, 28, 200000],
            ['Sahan Wickramasinghe', 'sahan.wickramasinghe@renovahub.test', 'Interior Designer', ['Luxury', 'Contemporary', 'Renovation'], 4.6, 19, false, true, 'Creative designer with experience in contemporary and luxury interiors. Works closely with clients to create personalised, functional spaces.', 10, 24, 260000],
            ['Imara Perera', 'imara.perera@renovahub.test', 'Interior Designer', ['Scandinavian', 'Minimal', 'Commercial'], 4.7, 22, false, true, 'Specialises in modern, Scandinavian and minimalist designs for homes and commercial spaces.', 8, 30, 210000],
            ['Roshel Perera', 'roshel.designer@renovahub.test', 'Interior Designer', ['Residential', 'Modern'], 4.7, 20, false, false, 'Residential designer working on family homes and villas.', 8, 20, 220000],
        ];

        $contractors = [
            ['Lanka Build Co', 'lankabuild@renovahub.test', 'Renovation Contractor', ['Residential Renovation', 'Full Home Renovation', 'Kitchen Renovation'], 4.8, 36, true, true, 'Residential renovation and kitchen fit-out team based in Colombo.', 12, 48, 900000],
            ['Harbor Construction', 'harbor.construction@renovahub.test', 'Construction Lead', ['Commercial Renovation', 'Construction Management'], 4.7, 22, true, true, 'Commercial renovation and construction management across the Western Province.', 15, 40, 1500000],
            ['FineFinish Works', 'finefinish@renovahub.test', 'Finishes Contractor', ['Interior Renovation', 'Material & Finishing'], 4.9, 29, true, true, 'Interior finishes, joinery and material installation.', 11, 34, 750000],
            ['Urban Frame Builders', 'urbanframe@renovahub.test', 'Project Execution', ['Project Execution', 'Full Home Renovation'], 4.5, 14, true, true, 'Full-home execution from first fix to handover.', 9, 18, 1100000],
            ['Coastal Renovators', 'coastal.renovators@renovahub.test', 'Renovation Contractor', ['Residential Renovation', 'Kitchen Renovation'], 4.6, 17, false, true, 'Kitchens, bathrooms and coastal family homes.', 10, 21, 680000],
            ['Prime Site Lanka', 'primesite@renovahub.test', 'Construction Lead', ['Construction Management', 'Commercial Renovation'], 4.4, 11, false, true, 'Site management for commercial and mixed-use renovations.', 13, 25, 1300000],
            ['Atelier Build', 'atelier.build@renovahub.test', 'Interior Renovation', ['Interior Renovation', 'Material & Finishing'], 4.8, 20, false, true, 'Careful interior renovation and finishing for design-led homes.', 8, 19, 820000],
            ['Anne Fernando', 'anne.fernando@renovahub.test', 'Renovation Contractor', ['Residential Renovation', 'Kitchen Renovation'], 4.6, 15, false, false, 'Hands-on contractor for family home renovations.', 12, 26, 950000],
        ];

        foreach ($designers as $index => $row) {
            $this->make('designer', $row, $images[$index % count($images)], $images);
        }

        foreach ($contractors as $index => $row) {
            $this->make('contractor', $row, $images[( $index + 3) % count($images)], $images);
        }
    }

    /**
     * @param  array{0: string, 1: string, 2: string, 3: list<string>, 4: float, 5: int, 6: bool, 7: bool, 8: string, 9: int, 10: int, 11: int}  $row
     * @param  list<string>  $images
     */
    private function make(string $role, array $row, string $avatar, array $images): void
    {
        [$name, $email, $title, $tags, $rating, $reviews, $featured, $listed, $bio, $years, $completed, $price] = $row;

        $user = User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make('professional-demo-only'),
                'role' => $role,
                'email_verified_at' => now(),
            ],
        );

        $profile = $user->professionalProfile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'professional_type' => $role,
                'business_name' => $role === 'contractor' ? $name : null,
                'title' => $title,
                'specialization' => implode(', ', $tags),
                'tags' => $tags,
                'bio' => $bio,
                'location' => 'Colombo',
                'avatar_path' => $avatar,
                'cover_path' => $images[array_search($avatar, $images, true) !== false ? (array_search($avatar, $images, true) + 1) % count($images) : 0],
                'years_experience' => $years,
                'completed_projects_count' => $completed,
                'rating' => $rating,
                'review_count' => $reviews,
                'starting_price' => $price,
                'listed' => $listed,
                'featured' => $featured,
                'verified' => $listed,
            ],
        );

        if ($listed) {
            $profile->services()->delete();
            foreach ($tags as $tag) {
                $profile->services()->create(['name' => $tag]);
            }

            $profile->portfolioItems()->delete();
            foreach (array_slice($images, 0, 3) as $i => $image) {
                $profile->portfolioItems()->create([
                    'title' => $tags[0].' project '.($i + 1),
                    'description' => 'A completed '.$role.' project in Colombo.',
                    'image' => $image,
                    'category' => $tags[0],
                    'location' => 'Colombo',
                    'budget_min' => 800000 + ($i * 400000),
                    'budget_max' => 1400000 + ($i * 400000),
                    'completion_year' => 2024 - $i,
                ]);
            }
        }
    }
}
