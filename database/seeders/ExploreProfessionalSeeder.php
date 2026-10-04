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
            ['Amaya Senarath', 'amaya.senarath@renovahub.test', 'Interior Designer', ['Modern', 'Minimal', 'Contemporary'], 4.8, 42, true, true, 'Interior designer with a focus on modern and functional living spaces. I specialise in residential renovations, space planning and creating personalised designs that match your lifestyle and budget.', 6, 50, 180000],
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
            $this->make('contractor', $row, $images[($index + 2) % count($images)], $images);
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
                'about' => $this->about($role, $name, $bio),
                'location' => 'Colombo',
                'avatar_path' => $avatar,
                'cover_path' => $role === 'contractor' ? $avatar : $images[0],
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
            : ($role === 'contractor'
                ? ['Residential Renovation', 'Full Home Renovation', 'Kitchen Renovation', 'Commercial Renovation', 'Construction Management', 'Project Execution', 'Material & Finishing']
                : $tags);
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
        $images = $this->photoPool();

        $rows = [
            ['Modern Villa Renovation', 'Residential', 'Colombo', 'Full Home Renovation', 3200, 28500000, '2025-03-01', 0, true, 1, 'A complete interior and exterior renovation of a two-storey villa, focusing on modern and functional living spaces.', 'The goal of this project was to transform an older villa into a modern, spacious and functional home while maintaining a warm and elegant aesthetic. The design focused on open spaces, natural lighting and modern details including kitchen and bathroom upgrades, custom cabinetry and an outdoor living area.', 'Dilan Perera', 'Homeowner, Colombo', 5.0, 'Amaya understood our vision perfectly and transformed our villa into a modern and beautiful home.'],
            ['Apartment Renovation', 'Residential', 'Colombo', 'Apartment renovation', 1800, 12500000, '2024-11-01', 1, false, 2, 'A compact apartment opened into one living kitchen with custom storage along every wall.', 'The apartment had been subdivided into dark rooms. We removed two partitions, aligned the kitchen with the balcony and used pale oak to bounce light from the west windows.', 'Nisha Fernando', 'Homeowner, Colombo', 4.9, 'The apartment feels twice as large. Storage is everywhere and nothing looks bulky.'],
            ['Minimalist Family Home', 'Residential', 'Kandy', 'Family home renovation', 2400, 9800000, '2024-08-01', 2, false, 3, 'A hillside family house calmed with fewer finishes and a stronger connection to the garden.', 'Joinery was reduced to one timber family, floors were unified and the kitchen moved to the view.', 'Ishara Jayawardena', 'Homeowner, Kandy', 4.8, 'The house is quieter and easier to live in. Guests always comment on the garden connection.'],
            ['Luxury Penthouse', 'Residential', 'Colombo', 'Penthouse interior', 2100, 22000000, '2024-04-01', 3, false, 4, 'A high-floor penthouse with a gallery living room, a hidden bar and bronze lighting.', 'The brief asked for hospitality without a hotel look. We used stone, bronze and forest-green millwork.', 'Rajan De Silva', 'Homeowner, Colombo', 5.0, 'Evenings in the living room feel like a private lounge. The detailing is exceptional.'],
            ['Office Interior Design', 'Commercial', 'Colombo', 'Office interior', 4000, 15000000, '2023-12-01', 4, false, 5, 'A workplace floor with acoustic meeting suites and a staff café on the street edge.', 'The core was rebuilt for focus rooms. Open desks sit in daylight and the café doubles as an informal meeting zone.', 'Priya Mendis', 'Facilities lead, Colombo', 4.7, 'Staff actually use the café. Noise in the open floor dropped after the acoustic work.'],
            ['Scandinavian Home', 'Residential', 'Galle', 'Scandinavian interior', 1900, 8700000, '2023-09-01', 5, false, 6, 'A Galle townhouse with limewashed walls, pale timber and a garden dining room.', 'We kept the colonial openings and introduced a lighter palette so the humidity of the south still feels cool indoors.', 'Elena Perera', 'Homeowner, Galle', 4.9, 'The house is bright without being stark. We live on the verandah most evenings.'],
        ];

        foreach ($rows as $row) {
            [$title, $category, $location, $type, $size, $budget, $completed, $offset, $featured, $order, $summary, $overview, $client, $clientPlace, $rating, $comment] = $row;
            $this->storeCase($profile, $images, $title, $category, $location, $type, $size, $budget, $completed, $offset, $featured, $order, $summary, $overview, $client, $clientPlace, $rating, $comment, true);
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
        $titles = [
            [$category.' Studio', 'A working studio organised around daylight, storage and a calm finish palette.'],
            [$category.' Residence', 'A family residence where rooms were opened, services upgraded and the garden brought closer.'],
            [$category.' Refresh', 'A lighter refresh of existing rooms with new joinery, lighting and wet-area finishes.'],
            [$category.' Annex', 'A small annex added for guests, with its own bath and a shared courtyard.'],
        ];
        $clients = ['Anika Perera', 'Ruwan Silva', 'Maya Fernando', 'Collin Jayasuriya'];

        foreach ($titles as $i => [$title, $summary]) {
            $this->storeCase(
                $profile,
                $images,
                $title,
                $category,
                'Colombo',
                $category,
                1800 + ($i * 220),
                4500000 + ($i * 800000),
                now()->subMonths(8 + $i)->toDateString(),
                $i,
                $i === 0,
                $i + 1,
                $summary,
                $summary.' The sequence ran from measured survey and mood board through shop drawings, site works and a documented handover.',
                $clients[$i],
                'Colombo',
                min(5, 4.6 + ($i * 0.1)),
                'The team stayed organised and the finished rooms match what we signed off on the mood board.',
                false,
            );
        }
    }

    /**
     * @param  list<string>  $images
     */
    private function storeCase(
        ProfessionalProfile $profile,
        array $images,
        string $title,
        string $category,
        string $location,
        string $type,
        int $size,
        int $budget,
        string $completed,
        int $offset,
        bool $featured,
        int $order,
        string $summary,
        string $overview,
        string $client,
        string $clientPlace,
        float $rating,
        string $comment,
        bool $plainSlug,
    ): void {
        $hero = $images[$offset % count($images)];
        $project = $profile->caseStudies()->create([
            'slug' => $plainSlug ? Str::slug($title) : Str::slug($profile->id.'-'.$title),
            'title' => $title,
            'category' => $category,
            'location' => $location,
            'property_type' => $category === 'Commercial' ? 'Commercial' : 'Residential',
            'project_type' => $type,
            'size_sq_ft' => $size,
            'budget' => $budget,
            'completed_on' => $completed,
            'summary' => $summary,
            'overview' => $overview,
            'highlights' => ['Clear planning sequence', 'Material palette held through construction', 'Site coordination with the build team', 'Documented handover'],
            'process' => [
                ['title' => 'Concept & Mood Board', 'body' => 'Direction set with the client before drawings were frozen.', 'image' => $images[($offset + 1) % count($images)]],
                ['title' => 'Design Development', 'body' => 'Layouts, services and finishes confirmed.', 'image' => $images[($offset + 2) % count($images)]],
                ['title' => 'Implementation', 'body' => 'Joinery, wet areas and site finishing.', 'image' => $images[($offset + 3) % count($images)]],
                ['title' => 'Final Result', 'body' => 'Handover of the completed rooms.', 'image' => $hero],
            ],
            'mood_board' => $this->moodBoard($offset),
            'materials' => ['Travertine', 'Oak', 'Green marble', 'Linen', 'Bronze'],
            'client_name' => $client,
            'client_location' => $clientPlace,
            'client_rating' => $rating,
            'client_body' => $comment,
            'hero_image' => $hero,
            'featured' => $featured,
            'sort_order' => $order,
        ]);

        for ($n = 0; $n < 5; $n++) {
            $project->images()->create([
                'path' => $images[($offset + $n) % count($images)],
                'sort_order' => $n,
                'caption' => $title.' view '.($n + 1),
                'image_type' => $n === 0 ? 'hero' : 'gallery',
            ]);
        }
    }

    /**
     * @return list<string>
     */
    private function photoPool(): array
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

    /**
     * @return array<string, mixed>
     */
    private function moodBoard(int $offset): array
    {
        $photos = $this->photoPool();
        $palettes = [
            [
                ['name' => 'Sage Green', 'hex' => '#5A6B4F'],
                ['name' => 'Warm Beige', 'hex' => '#D9C9B6'],
                ['name' => 'Wood Brown', 'hex' => '#8B6F47'],
                ['name' => 'Charcoal', 'hex' => '#2C2C2C'],
                ['name' => 'Off White', 'hex' => '#F7F5F0'],
            ],
            [
                ['name' => 'Clay', 'hex' => '#C4A484'],
                ['name' => 'Ivory', 'hex' => '#F4EFE6'],
                ['name' => 'Slate', 'hex' => '#4A5759'],
                ['name' => 'Olive', 'hex' => '#6B705C'],
                ['name' => 'Sand', 'hex' => '#E6D5B8'],
            ],
            [
                ['name' => 'Forest', 'hex' => '#123D2B'],
                ['name' => 'Stone', 'hex' => '#C2B8A3'],
                ['name' => 'Bronze', 'hex' => '#8C6A4A'],
                ['name' => 'Ink', 'hex' => '#1F1F1F'],
                ['name' => 'Linen', 'hex' => '#F3EEE4'],
            ],
        ];

        return [
            'headline' => $offset % 2 === 0
                ? 'Natural, elegant and contemporary direction for this interior.'
                : 'Warm stone, timber and metal references unique to this project.',
            'palette' => $palettes[$offset % count($palettes)],
            'materials' => [
                ['name' => 'Travertine', 'image' => $photos[($offset) % count($photos)]],
                ['name' => 'Oak wood', 'image' => $photos[($offset + 1) % count($photos)]],
                ['name' => 'Green marble', 'image' => $photos[($offset + 2) % count($photos)]],
                ['name' => 'Linen fabric', 'image' => $photos[($offset + 3) % count($photos)]],
                ['name' => 'Bronze metal', 'image' => $photos[($offset + 4) % count($photos)]],
            ],
            'inspiration' => [
                $photos[($offset) % count($photos)],
                $photos[($offset + 2) % count($photos)],
                $photos[($offset + 4) % count($photos)],
            ],
        ];
    }

    private function about(string $role, string $name, string $bio): string
    {
        if ($role === 'contractor') {
            return $bio."\n\n".$name.' operates with a dedicated site team, a finishing crew and a planning lead who coordinates with the appointed designer. The company is set up for residential and light commercial renovations in Colombo and the Western Province, with a clear sequence from measured survey to handover.';
        }

        return $bio."\n\nI work with homeowners from the first brief through construction, producing space plans, mood boards, joinery details and site reviews. Each project is designed around how the family actually lives rather than a single signature look.";
    }
}
