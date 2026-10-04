<?php

namespace Database\Seeders;

use App\Models\ProfessionalProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ExploreProfessionalSeeder extends Seeder
{
    /**
     * Directory cards, profile pages and case studies all read from these
     * rows so Blade never hardcodes Amaya's villa or the featured four.
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

        $portraits = [
            'amaya.senarath@renovahub.test' => 'images/professionals/amaya-senarath.jpg',
            'dinesh.jayawardena@renovahub.test' => 'images/professionals/dinesh-jayawardena.jpg',
            'sithumi.fernando@renovahub.test' => 'images/professionals/sithumi-fernando.jpg',
            'kavinda.perera@renovahub.test' => 'images/professionals/kavinda-perera.jpg',
            'nethmi.wijesinghe@renovahub.test' => 'images/professionals/nethmi-wijesinghe.jpg',
            'sahan.wickramasinghe@renovahub.test' => 'images/professionals/sahan-wickramasinghe.jpg',
            'imara.perera@renovahub.test' => 'images/professionals/imara-perera.jpg',
        ];

        $designers = [
            ['Amaya Senarath', 'amaya.senarath@renovahub.test', 'Interior Designer', ['Modern', 'Minimal', 'Contemporary'], 4.8, 42, true, true, 'Interior designer with a focus on modern and functional living spaces. I specialise in residential renovations, space planning and creating personalised designs that match your lifestyle and budget.', 6, 50, 5000],
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
            $this->make('designer', $row, $portraits[$row[1]] ?? $images[$index % count($images)], $images);
        }

        foreach ($contractors as $index => $row) {
            $this->make('contractor', $row, $images[($index + 3) % count($images)], $images);
        }

        $this->amayaCaseStudies();
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
                'cover_path' => $images[0],
                'years_experience' => $years,
                'completed_projects_count' => $completed,
                'client_satisfaction' => 95,
                'rating' => $rating,
                'review_count' => $reviews,
                'starting_price' => $price,
                'listed' => $listed,
                'featured' => $featured,
                'verified' => $listed,
            ],
        );

        if (! $listed) {
            return;
        }

        $profile->services()->delete();
        $serviceNames = $email === 'amaya.senarath@renovahub.test'
            ? ['Residential Interiors', 'Space Planning', 'Modern & Minimal Design', '3D Visualization', 'Furniture Selection', 'Renovation Consultation']
            : $tags;
        foreach ($serviceNames as $tag) {
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

        if ($email !== 'amaya.senarath@renovahub.test') {
            $this->genericCaseStudies($profile, $images, $tags[0] ?? 'Residential');
        }
    }

    private function amayaCaseStudies(): void
    {
        $profile = User::query()->where('email', 'amaya.senarath@renovahub.test')->firstOrFail()->professionalProfile;
        $profile->caseStudies()->delete();
        $profile->reviews()->delete();

        $rows = [
            ['Modern Villa Renovation', 'Residential', 'Colombo', 'Residential', 'Full Home Renovation', 3200, 28500000, '2025-03-01', 'images/renova/about-interior.jpg', true, 1],
            ['Apartment Renovation', 'Residential', 'Colombo', 'Residential', 'Apartment renovation', 1800, 12500000, '2024-11-01', 'images/renova/feature-collab.jpg', false, 2],
            ['Minimalist Family Home', 'Residential', 'Kandy', 'Residential', 'Family home renovation', 2400, 9800000, '2024-08-01', 'images/renova/feature-green.jpg', false, 3],
            ['Luxury Penthouse', 'Residential', 'Colombo', 'Residential', 'Penthouse interior', 2100, 22000000, '2024-04-01', 'images/renova/auth-register.jpg', false, 4],
            ['Office Interior Design', 'Commercial', 'Colombo', 'Commercial', 'Office interior', 4000, 15000000, '2023-12-01', 'images/renova/feature-progress.jpg', false, 5],
            ['Scandinavian Home', 'Residential', 'Galle', 'Residential', 'Scandinavian interior', 1900, 8700000, '2023-09-01', 'images/renova/feature-plans.jpg', false, 6],
        ];

        foreach ($rows as $row) {
            [$title, $category, $location, $property, $type, $size, $budget, $completed, $hero, $featured, $order] = $row;
            $project = $profile->caseStudies()->create([
                'slug' => Str::slug($title),
                'title' => $title,
                'category' => $category,
                'location' => $location,
                'property_type' => $property,
                'project_type' => $type,
                'size_sq_ft' => $size,
                'budget' => $budget,
                'completed_on' => $completed,
                'summary' => $title === 'Modern Villa Renovation'
                    ? 'A complete interior and exterior renovation of a two-storey villa, focusing on modern and functional living spaces. The project included living areas, bedrooms, kitchen, outdoor spaces and landscaping, designed to create a warm, modern and elegant home.'
                    : 'A completed interior project designed around light, storage and a calm material palette.',
                'overview' => 'The goal of this project was to transform an older villa into a modern, spacious and functional home while maintaining a warm and elegant aesthetic. The design focused on open spaces, natural lighting and modern details. The project included complete interior renovation, kitchen and bathroom upgrades, custom cabinetry, lighting design and an outdoor living area with landscaping.',
                'highlights' => [
                    'Modern and functional interior design',
                    'Open-plan living and dining area',
                    'Custom kitchen and storage solutions',
                    'Upgraded bathrooms with modern finishes',
                    'Outdoor seating area and landscaping',
                ],
                'process' => [
                    ['title' => 'Concept & Mood Board', 'body' => 'Initial concepts and design direction based on the client’s requirements.', 'image' => 'images/renova/feature-green.jpg'],
                    ['title' => 'Design Development', 'body' => 'Finalized layouts, materials and colour scheme.', 'image' => 'images/renova/feature-plans.jpg'],
                    ['title' => 'Implementation', 'body' => 'Detailed execution with custom furniture and finishes.', 'image' => 'images/renova/feature-docs.jpg'],
                    ['title' => 'Final Result', 'body' => 'A modern, elegant and functional home completed successfully.', 'image' => 'images/renova/about-interior.jpg'],
                ],
                'hero_image' => $hero,
                'featured' => $featured,
                'sort_order' => $order,
            ]);

            foreach (['images/renova/about-exterior.jpg', 'images/renova/feature-collab.jpg', 'images/renova/feature-progress.jpg', 'images/renova/feature-green.jpg'] as $i => $path) {
                $project->images()->create(['path' => $path, 'sort_order' => $i]);
            }
        }

        $profile->reviews()->create([
            'author_name' => 'Dilan Perera',
            'project_title' => 'Homeowner, Colombo',
            'body' => 'Amaya was amazing to work with. She understood our vision perfectly and transformed our villa into a modern and beautiful home. The whole process was smooth and professional.',
            'rating' => 5.0,
        ]);
    }

    /**
     * @param  list<string>  $images
     */
    private function genericCaseStudies(ProfessionalProfile $profile, array $images, string $category): void
    {
        $profile->caseStudies()->delete();
        $titles = [$category.' Studio', $category.' Residence', $category.' Refresh'];

        foreach ($titles as $i => $title) {
            $profile->caseStudies()->create([
                'slug' => Str::slug($profile->id.'-'.$title),
                'title' => $title,
                'category' => $category,
                'location' => 'Colombo',
                'property_type' => 'Residential',
                'project_type' => $category,
                'size_sq_ft' => 1800 + ($i * 200),
                'budget' => 4500000 + ($i * 800000),
                'completed_on' => now()->subMonths(8 + $i)->toDateString(),
                'summary' => 'A completed '.$category.' project in Colombo.',
                'overview' => 'The work focused on daylight, storage and a calm finish palette.',
                'highlights' => ['Practical layout', 'Durable finishes', 'Clear budget control'],
                'process' => [
                    ['title' => 'Concept & Mood Board', 'body' => 'Direction set with the client.', 'image' => $images[0]],
                    ['title' => 'Design Development', 'body' => 'Layouts and materials confirmed.', 'image' => $images[1]],
                    ['title' => 'Implementation', 'body' => 'Site work and joinery.', 'image' => $images[2]],
                    ['title' => 'Final Result', 'body' => 'Handover of the completed rooms.', 'image' => $images[3]],
                ],
                'hero_image' => $images[$i % count($images)],
                'featured' => $i === 0,
                'sort_order' => $i + 1,
            ]);
        }
    }
}
