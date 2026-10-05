<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\BudgetSubmission;
use App\Models\ContractorEarning;
use App\Models\DesignerEarning;
use App\Models\Payment;
use App\Models\PaymentAllocation;
use App\Models\ChangeRequest;
use App\Models\Conversation;
use App\Models\Document;
use App\Models\ProfessionalProfile;
use App\Models\Project;
use App\Models\ProjectInvitation;
use App\Models\ProjectTask;
use App\Models\Quotation;
use App\Models\ConstructionAssignment;
use App\Models\ConstructionFirm;
use App\Models\ConstructionFirmQuotation;
use App\Models\DesignConcept;
use App\Models\MaterialRequirement;
use App\Models\ProcurementProposal;
use App\Models\Supplier;
use App\Models\SupplierCategory;
use App\Models\SupplierOrder;
use App\Models\SupplierPriceRequest;
use App\Models\User;
use App\Services\Contractor\ProcurementService;
use App\Services\Contractor\ProjectBudgetService;
use App\Services\Contractor\SupplierOrderService;
use App\Services\Contractor\SupplierService;
use App\Services\ConversationService;
use App\Services\NotificationService;
use App\Services\PaymentService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

/**
 * Kavinda Perera's contractor workspace.
 * Records are matched by email, slug or project address so seeding again
 * does not duplicate the demonstration.
 */
class ContractorWorkspaceSeeder extends Seeder
{
    public function run(): void
    {
        $kavinda = User::query()->updateOrCreate(
            ['email' => 'contractor@test.com'],
            [
                'name' => 'Kavinda Perera',
                'password' => Hash::make('contractor@123'),
                'role' => 'contractor',
                'phone' => '0775550188',
                'address' => '24 Duplication Road, Colombo 04',
                'email_verified_at' => now(),
            ],
        );

        $profile = ProfessionalProfile::query()->updateOrCreate(
            ['user_id' => $kavinda->id],
            [
                'professional_type' => 'contractor',
                'business_name' => 'Perera & Co',
                'title' => 'Renovation contractor',
                'specialization' => 'Interior construction, kitchens and villas',
                'bio' => 'Kavinda leads residential renovation builds across Colombo and the south coast.',
                'about' => 'Perera & Co manages quotations, suppliers and site progress after the design is approved.',
                'location' => 'Colombo, Sri Lanka',
                'years_experience' => 12,
                'completed_projects_count' => 46,
                'rating' => 4.8,
                'review_count' => 32,
                'listed' => true,
                'verified' => true,
                'avatar_path' => 'images/renova/about-exterior.jpg',
                'cover_path' => 'images/renova/hero.jpg',
            ],
        );

        $homeowner = User::query()->where('email', 'homeowner@test.com')->firstOrFail();
        $designer = User::query()->where('email', 'designer@test.com')->first()
            ?? User::query()->where('role', 'designer')->first();

        $support = User::query()->firstOrCreate(
            ['email' => 'support@renovahub.test'],
            [
                'name' => 'RenovaHub Support',
                'password' => Hash::make('password'),
                'role' => 'homeowner',
                'email_verified_at' => now(),
            ],
        );

        $photos = [
            'images/renova/about-interior.jpg',
            'images/renova/feature-plans.jpg',
            'images/renova/feature-green.jpg',
            'images/renova/about-exterior.jpg',
            'images/renova/hero.jpg',
        ];

        $apartment = $this->project($homeowner, $designer, $kavinda, 'Apartment Interior Makeover', 'Colombo 07', 'Apartment', 'apartment', 'A full interior makeover with a new kitchen, timber flooring and custom storage.', 48, Project::STATUS_IN_PROGRESS, 'Kitchen installation', '2026-10-18', $photos, true, 12500000);
        $coastal = $this->project($homeowner, $designer, $kavinda, 'Coastal Villa Renovation', 'Galle', 'Villa', 'villa', 'Open the villa to the garden, replace bathrooms and extend the pool deck.', 36, Project::STATUS_IN_PROGRESS, 'Pool deck review', '2026-11-02', array_reverse($photos), true, 18600000);
        $valley = $this->project($homeowner, $designer, $kavinda, 'Green Valley Residence', 'Colombo', 'House', 'house', 'A family house opening toward the garden with warm timber and soft sage walls.', 22, Project::STATUS_PLANNING, 'Site set-out', '2026-11-20', $photos, true, 9800000);
        $this->project($homeowner, $designer, $kavinda, 'Modern House Renovation', 'Negombo', 'House', 'house', 'A pending offer for a full-house renovation. The contractor has not accepted yet.', 0, Project::STATUS_AWAITING_TEAM, 'Team confirmation', '2026-12-01', $photos, false, 15000000, ProjectInvitation::STATUS_PENDING);
        $this->project($homeowner, $designer, $kavinda, 'Beach House Construction', 'Bentota', 'Villa', 'villa', 'A declined offer. The homeowner can invite another contractor.', 0, Project::STATUS_AWAITING_TEAM, 'Team confirmation', '2026-12-15', $photos, false, 22000000, ProjectInvitation::STATUS_DECLINED);

        $this->budget($apartment, [
            ['Design & Planning', 800000, 100],
            ['Materials/Supplier Costs', 5200000, 40],
            ['Labour', 2800000, 35],
            ['Equipment', 600000, 20],
            ['Fixtures & Furniture', 1400000, 10],
            ['Additional Changes', 0, 0],
            ['Contingency', 700000, 0],
        ]);
        $this->budget($coastal, [
            ['Design & Planning', 900000, 80],
            ['Materials/Supplier Costs', 7400000, 15],
            ['Labour', 4100000, 10],
            ['Equipment', 800000, 0],
            ['Fixtures & Furniture', 2200000, 0],
            ['Additional Changes', 650000, 0],
            ['Contingency', 1100000, 0],
        ]);
        $this->budget($valley, [
            ['Design & Planning', 500000, 40],
            ['Materials/Supplier Costs', 3600000, 0],
            ['Labour', 2400000, 0],
            ['Equipment', 400000, 0],
            ['Fixtures & Furniture', 1200000, 0],
            ['Additional Changes', 0, 0],
            ['Contingency', 700000, 0],
        ]);

        $this->quotation($apartment, $kavinda, 'QTN-APT-01', Quotation::STATUS_APPROVED, 'Kitchen renovation package', [
            ['Kitchen Cabinets', 'materials', 1, 850000],
            ['Flooring', 'materials', 1, 450000],
            ['Labour', 'labour', 1, 300000],
            ['Electrical', 'other', 1, 120000],
        ]);
        $this->quotation($coastal, $kavinda, 'QTN-CST-01', Quotation::STATUS_SUBMITTED, 'Villa construction quotation awaiting the homeowner.', [
            ['Structural work', 'labour', 1, 2400000],
            ['Bathrooms', 'materials', 2, 680000],
        ]);
        $this->quotation($valley, $kavinda, 'QTN-GV-01', Quotation::STATUS_DRAFT, 'Draft quotation, not yet sent to the homeowner.', [
            ['Site preparation', 'labour', 1, 180000],
        ]);

        $supplier = $this->supplier();
        $this->orders($kavinda, $apartment, $supplier);
        $this->tasks($apartment, $coastal, $kavinda);
        $this->documents($apartment, $kavinda);
        $this->change($coastal, $homeowner);
        $this->messages($homeowner, $designer, $kavinda, $support, $apartment);
        $this->activity($apartment, $coastal, $homeowner, $kavinda);
        $this->procurement($homeowner, $designer, $kavinda, $apartment, $coastal);
        $this->showcasePayments($homeowner, $designer, $kavinda, $apartment, $coastal, $valley);
        $this->portfolio($profile);
        $this->notify($kavinda, $apartment);
    }

    /**
     * Approved design, material list, firm comparison and one budget.
     * Homeowner approval is applied through the same services the UI uses.
     */
    private function procurement(User $homeowner, ?User $designer, User $contractor, Project $apartment, Project $coastal): void
    {
        $apartment->budgetItems()->updateOrCreate(
            ['category' => 'Contractor Fee'],
            ['amount' => 350000, 'spent_percent' => 0, 'sort_order' => 8],
        );

        $concept = DesignConcept::query()->updateOrCreate(
            ['project_id' => $apartment->id, 'title' => 'Approved final design package'],
            [
                'designer_id' => $designer?->id ?? $homeowner->id,
                'description' => 'Kitchen, living room and timber flooring approved for construction.',
                'notes' => 'Use the warm oak sample and the soft-close cabinet specification.',
                'status' => DesignConcept::STATUS_APPROVED,
                'submitted_at' => now()->subDays(12),
                'approved_at' => now()->subDays(8),
            ],
        );

        if ($concept->files()->doesntExist()) {
            foreach ([
                'images/renova/about-interior.jpg',
                'images/renova/feature-plans.jpg',
                'images/renova/feature-green.jpg',
                'images/renova/hero.jpg',
                'images/renova/feature-docs.jpg',
            ] as $index => $path) {
                $concept->files()->create([
                    'kind' => $index === 1 ? 'floor_plan' : 'image',
                    'path' => $path,
                    'caption' => 'Approved view '.($index + 1),
                ]);
            }
        }

        $this->materials($apartment, $concept, 18);
        $coastalConcept = DesignConcept::query()->updateOrCreate(
            ['project_id' => $coastal->id, 'title' => 'Approved final design package'],
            [
                'designer_id' => $designer?->id ?? $homeowner->id,
                'description' => 'Coastal villa package approved for construction.',
                'notes' => 'Salt-resistant finishes and a quieter bedroom wing.',
                'status' => DesignConcept::STATUS_APPROVED,
                'submitted_at' => now()->subDays(6),
                'approved_at' => now()->subDays(2),
            ],
        );
        $this->materials($coastal, $coastalConcept, 14);
        $valley = Project::query()->where('name', 'Green Valley Residence')->where('address', 'Contractor workspace demo')->first();
        if ($valley) {
            $valleyConcept = DesignConcept::query()->updateOrCreate(
                ['project_id' => $valley->id, 'title' => 'Approved final design package'],
                [
                    'designer_id' => $designer?->id ?? $homeowner->id,
                    'description' => 'Garden residence package approved for construction.',
                    'notes' => 'Warm timber and sage walls toward the garden.',
                    'status' => DesignConcept::STATUS_APPROVED,
                    'submitted_at' => now()->subDays(9),
                    'approved_at' => now()->subDays(5),
                ],
            );
            $this->materials($valley, $valleyConcept, 12);
        }

        $photos = ['images/renova/about-exterior.jpg', 'images/renova/feature-tasks.jpg', 'images/renova/feature-progress.jpg'];
        $firmRows = [
            ['buildright-construction', 'BuildRight Construction', 'Colombo', 'Structural and fit-out', 4.7, 86, 14, 120, 1800000],
            ['urban-builders', 'Urban Builders', 'Colombo', 'Interior construction', 4.8, 64, 11, 90, 1650000],
            ['coastal-structures', 'Coastal Structures', 'Galle', 'Villas and extensions', 4.6, 41, 9, 70, 1950000],
            ['prime-structures', 'Prime Structures', 'Negombo', 'Residential builds', 4.5, 33, 8, 55, 1500000],
        ];

        foreach ($firmRows as [$slug, $name, $city, $spec, $rating, $reviews, $years, $done, $start]) {
            ConstructionFirm::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'city' => $city,
                    'location' => $city.', Sri Lanka',
                    'specialisation' => $spec,
                    'about' => $name.' builds residential renovations with a site team, programme and defect liability period.',
                    'rating' => $rating,
                    'review_count' => $reviews,
                    'years_experience' => $years,
                    'completed_projects' => $done,
                    'starting_price' => $start,
                    'portfolio' => $photos,
                    'highlights' => ['Licensed site team', 'Written programme', 'Defect liability included'],
                ],
            );
        }

        $procurement = app(ProcurementService::class);

        if (ConstructionFirmQuotation::query()->where('project_id', $apartment->id)->doesntExist()) {
            $offers = [
                ['buildright-construction', 1800000, 90],
                ['urban-builders', 1650000, 105],
                ['coastal-structures', 1950000, 75],
            ];
            foreach ($offers as [$slug, $price, $days]) {
                $procurement->recordFirmQuote($contractor, $apartment, [
                    'construction_firm_id' => ConstructionFirm::query()->where('slug', $slug)->value('id'),
                    'price' => $price,
                    'duration_days' => $days,
                    'start_date' => '2026-10-20',
                    'completion_date' => now()->parse('2026-10-20')->addDays($days)->toDateString(),
                    'scope' => 'Labour, site supervision and finishing to the approved design.',
                    'terms' => 'Mobilisation after the homeowner payment is verified.',
                    'warranty' => '12 months',
                    'notes' => null,
                ]);
            }

            $ids = ConstructionFirmQuotation::query()->where('project_id', $apartment->id)->pluck('id')->all();
            $proposal = $procurement->sendFirmOptions($contractor, $apartment, $ids);
            $chosen = $proposal->options()->whereHas('quotation', fn ($query) => $query->where('price', 1650000))->first();
            $procurement->decide($homeowner, $proposal, 'approve', $chosen->id, 'Urban Builders matches the programme.');
            $procurement->assign($contractor, $apartment, $chosen->quotation);
        }

        if (! ProcurementProposal::query()->where('project_id', $coastal->id)->where('type', ProcurementProposal::TYPE_FIRM)->exists()
            && DesignConcept::query()->where('project_id', $coastal->id)->where('status', DesignConcept::STATUS_APPROVED)->doesntExist()) {
            DesignConcept::query()->create([
                'project_id' => $coastal->id,
                'designer_id' => $designer?->id ?? $homeowner->id,
                'title' => 'Coastal villa final design',
                'description' => 'Approved terrace and bathroom package.',
                'status' => DesignConcept::STATUS_APPROVED,
                'submitted_at' => now()->subDays(4),
                'approved_at' => now()->subDays(2),
            ]);
        }

        if (ConstructionFirmQuotation::query()->where('project_id', $coastal->id)->doesntExist()) {
            foreach (['buildright-construction', 'prime-structures'] as $index => $slug) {
                $procurement->recordFirmQuote($contractor, $coastal, [
                    'construction_firm_id' => ConstructionFirm::query()->where('slug', $slug)->value('id'),
                    'price' => 2400000 + ($index * 150000),
                    'duration_days' => 120,
                    'start_date' => '2026-11-02',
                    'completion_date' => '2027-03-02',
                    'scope' => 'Pool deck and bathroom construction.',
                    'terms' => 'Subject to homeowner approval.',
                    'warranty' => '12 months',
                    'notes' => null,
                ]);
            }
            $ids = ConstructionFirmQuotation::query()->where('project_id', $coastal->id)->pluck('id')->all();
            $procurement->sendFirmOptions($contractor, $coastal, $ids);
        }

        if (! $apartment->budgetSubmissions()->whereIn('status', ['awaiting_homeowner', 'approved'])->exists()
            && $apartment->procurementProposals()->where('type', 'supplier')->where('status', 'approved')->doesntExist()) {
            $price = \App\Models\SupplierPrice::query()
                ->whereHas('request', fn ($query) => $query->where('project_id', $apartment->id)->where('product', 'Custom Kitchen Cabinet Set'))
                ->first();

            if ($price) {
                $proposal = ProcurementProposal::query()->create([
                    'project_id' => $apartment->id,
                    'contractor_id' => $contractor->id,
                    'type' => ProcurementProposal::TYPE_SUPPLIER,
                    'status' => ProcurementProposal::STATUS_APPROVED,
                    'selected_supplier_price_id' => $price->id,
                    'submitted_at' => now()->subDay(),
                    'decided_at' => now()->subDay(),
                ]);
                $proposal->options()->create(['supplier_price_id' => $price->id]);
            }
        }

        if (! $apartment->budgetSubmissions()->whereIn('status', ['awaiting_homeowner', 'approved'])->exists()
            && ConstructionAssignment::query()->where('project_id', $apartment->id)->exists()) {
            app(ProjectBudgetService::class)->submit($contractor, $apartment->fresh());
        }
    }

    private function project(User $owner, ?User $designer, ?User $contractor, string $name, string $city, string $typeLabel, string $property, string $description, int $progress, string $status, string $milestone, string $due, array $photos, bool $accepted, float $budget, string $inviteStatus = ProjectInvitation::STATUS_ACCEPTED): Project
    {
        $project = $owner->projects()->firstOrNew([
            'name' => $name,
            'address' => 'Contractor workspace demo',
        ]);

        $project->fill([
            'description' => $description,
            'requirements' => $description,
            'renovation_type' => 'full_house',
            'property_type' => $property,
            'size_sq_ft' => 2100,
            'city' => $city,
            'province' => $city === 'Galle' || $city === 'Bentota' ? 'Southern Province' : 'Western Province',
            'estimated_budget' => $budget,
            'current_budget' => $budget,
            'currency' => 'LKR',
            'expected_start_date' => '2026-09-01',
            'expected_completion_date' => '2026-12-20',
            'status' => $status,
            'progress' => $progress,
            'cover_image' => $this->coverFor($name),
            'designer_id' => $designer?->id,
            'contractor_id' => $accepted ? $contractor?->id : null,
            'workspace_meta' => [
                'type_label' => $typeLabel,
                'next_milestone' => $milestone,
                'milestone_due' => $due,
            ],
        ]);
        $project->save();

        $project->referenceImages()->delete();
        foreach ($this->galleryFor($name) as $path) {
            $project->referenceImages()->create(['path' => $path, 'original_name' => basename($path)]);
        }

        $planning = $progress >= 30 ? 100 : max($progress, 10);
        $construction = $progress >= 30 ? $progress : 0;

        foreach (['design' => 100, 'planning' => $planning, 'construction' => $construction, 'inspection' => 0] as $stage => $percent) {
            $project->progressStages()->updateOrCreate(['stage' => $stage], ['percent' => $percent]);
        }

        $project->milestones()->updateOrCreate(
            ['title' => $milestone],
            ['due_on' => $due, 'description' => $milestone],
        );

        if ($contractor) {
            $project->invitations()->updateOrCreate(
                ['user_id' => $contractor->id, 'role' => 'contractor'],
                ['status' => $inviteStatus, 'responded_at' => $inviteStatus === ProjectInvitation::STATUS_PENDING ? null : now()->subWeeks(3)],
            );
        }

        if ($designer) {
            $project->invitations()->updateOrCreate(
                ['user_id' => $designer->id, 'role' => 'designer'],
                ['status' => ProjectInvitation::STATUS_ACCEPTED, 'responded_at' => now()->subMonth()],
            );
        }

        return $project;
    }

    /**
     * @param  list<array{0: string, 1: int, 2: int}>  $lines
     */
    private function budget(Project $project, array $lines): void
    {
        foreach ($lines as $index => [$category, $amount, $spent]) {
            $project->budgetItems()->updateOrCreate(
                ['category' => $category],
                ['amount' => $amount, 'spent_percent' => $spent, 'sort_order' => $index + 1],
            );
        }
    }

    /**
     * @param  list<array{0: string, 1: string, 2: float, 3: float}>  $rows
     */
    private function quotation(Project $project, User $contractor, string $number, string $status, string $description, array $rows): void
    {
        $materials = 0.0;
        $labour = 0.0;
        $additional = 0.0;

        foreach ($rows as [$item, $category, $quantity, $unit]) {
            $line = $quantity * $unit;
            if ($category === 'labour') {
                $labour += $line;
            } elseif ($category === 'materials') {
                $materials += $line;
            } else {
                $additional += $line;
            }
        }

        $quotation = $project->quotations()->updateOrCreate(
            ['number' => $number],
            [
                'contractor_id' => $contractor->id,
                'description' => $description,
                'materials' => $materials,
                'labour' => $labour,
                'additional_costs' => $additional,
                'subtotal' => $materials + $labour + $additional,
                'total' => $materials + $labour + $additional,
                'status' => $status,
                'valid_until' => '2026-11-30',
                'approved_at' => $status === Quotation::STATUS_APPROVED ? now()->subDays(6) : null,
            ],
        );

        if ($quotation->items()->count() !== count($rows)) {
            $quotation->items()->delete();
            foreach ($rows as [$item, $category, $quantity, $unit]) {
                $quotation->items()->create([
                    'item' => $item,
                    'category' => $category,
                    'quantity' => $quantity,
                    'unit_cost' => $unit,
                    'total' => $quantity * $unit,
                ]);
            }
        }
    }

    private function supplier(): Supplier
    {
        $supplier = Supplier::query()->updateOrCreate(
            ['slug' => 'abc-interiors'],
            [
                'name' => 'ABC Interiors',
                'location' => 'Colombo, Sri Lanka',
                'city' => 'Colombo',
                'service_area' => 'Western Province',
                'about' => 'ABC Interiors supplies flooring, kitchen cabinets and bathroom fixtures for renovation projects, with delivery and installation support.',
                'years_experience' => 8,
                'completed_orders' => 200,
                'rating' => 4.8,
                'review_count' => 128,
                'hero_path' => 'images/renova/about-interior.jpg',
                'why_choose' => ['Wide product range', 'Professional support', 'Competitive pricing', 'Quality materials', 'Reliable delivery'],
                'portfolio_images' => [
                    'images/renova/feature-plans.jpg',
                    'images/renova/feature-green.jpg',
                    'images/renova/about-interior.jpg',
                    'images/renova/feature-docs.jpg',
                    'images/renova/hero.jpg',
                ],
            ],
        );

        $categories = [
            'flooring-tiles' => 'Flooring & Tiles',
            'kitchen-cabinets' => 'Kitchen & Cabinets',
            'bathroom-fixtures' => 'Bathroom Fixtures',
            'lighting' => 'Lighting',
            'paint-finishes' => 'Paint & Finishes',
        ];

        $ids = [];
        foreach ($categories as $slug => $name) {
            $ids[] = SupplierCategory::query()->firstOrCreate(['slug' => $slug], ['name' => $name])->id;
        }
        $supplier->categories()->sync(array_slice($ids, 0, 3));

        foreach ([
            ['Flooring', 4500, 'm²', 1],
            ['Kitchen Cabinets', 850000, null, 2],
            ['Bathroom Fixtures', 120000, null, 3],
        ] as [$label, $amount, $unit, $sort]) {
            $supplier->startingPrices()->updateOrCreate(
                ['label' => $label],
                ['amount' => $amount, 'unit' => $unit, 'sort_order' => $sort],
            );
        }

        $supplier->reviews()->updateOrCreate(
            ['author_name' => 'Nadee Silva'],
            ['project_title' => 'Apartment kitchen', 'body' => 'Cabinets arrived on time and the finish matched the approved design.', 'rating' => 5],
        );

        foreach ([
            ['lanka-hardware', 'Lanka Hardware', 'Colombo', 'Hardware'],
            ['modern-lighting', 'Modern Lighting', 'Nugegoda', 'Lighting'],
            ['prime-timber', 'Prime Timber', 'Moratuwa', 'Wood Flooring'],
        ] as [$slug, $name, $city, $categoryName]) {
            $other = Supplier::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'location' => $city.', Sri Lanka',
                    'city' => $city,
                    'service_area' => 'Western Province',
                    'about' => $name.' supplies '.$categoryName.' for residential renovations.',
                    'years_experience' => 6,
                    'completed_orders' => 90,
                    'rating' => 4.6,
                    'review_count' => 40,
                    'hero_path' => 'images/renova/feature-docs.jpg',
                    'portfolio_images' => ['images/renova/feature-docs.jpg', 'images/renova/feature-tasks.jpg', 'images/renova/about-exterior.jpg'],
                    'why_choose' => ['Trade pricing', 'Island-wide delivery'],
                ],
            );
            $category = SupplierCategory::query()->firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($categoryName)],
                ['name' => $categoryName],
            );
            $other->categories()->syncWithoutDetaching([$category->id]);
            $other->startingPrices()->updateOrCreate(
                ['label' => $categoryName],
                ['amount' => 25000, 'unit' => null, 'sort_order' => 1],
            );
        }

        return $supplier;
    }

    private function orders(User $contractor, Project $project, Supplier $supplier): void
    {
        $pending = SupplierOrder::query()->where('contractor_id', $contractor->id)->where('item', 'Custom Kitchen Cabinet Set')->first();

        if ($pending === null) {
            $request = SupplierPriceRequest::query()->create([
                'project_id' => $project->id,
                'contractor_id' => $contractor->id,
                'supplier_id' => $supplier->id,
                'material' => 'Kitchen Cabinets',
                'product' => 'Custom Kitchen Cabinet Set',
                'quantity' => 1,
                'unit' => 'Set',
                'required_by' => '2026-10-20',
                'notes' => 'Preferred material, colour and delivery requirements.',
                'status' => SupplierPriceRequest::STATUS_SENT,
            ]);

            $price = app(SupplierService::class)->recordOffer($request, [
                'quoted_price' => 850000,
                'lead_time_days' => 7,
                'delivery' => 'Included',
                'warranty' => '2 years',
                'valid_until' => '2026-10-25',
                'message' => 'Price includes delivery and standard installation support.',
            ]);

            app(SupplierOrderService::class)->place($contractor, $price, $project->address, 'Deliver to the apartment site.');
        }

        $paid = SupplierOrder::query()->where('contractor_id', $contractor->id)->where('item', 'Oak Flooring Package')->first();

        if ($paid === null) {
            $request = SupplierPriceRequest::query()->create([
                'project_id' => $project->id,
                'contractor_id' => $contractor->id,
                'supplier_id' => $supplier->id,
                'material' => 'Flooring',
                'product' => 'Oak Flooring Package',
                'quantity' => 80,
                'unit' => 'm²',
                'required_by' => '2026-10-28',
                'notes' => 'Match the approved timber sample.',
                'status' => SupplierPriceRequest::STATUS_SENT,
            ]);
            $price = app(SupplierService::class)->recordOffer($request, [
                'quoted_price' => 500000,
                'lead_time_days' => 10,
                'delivery' => 'Included',
                'warranty' => '5 years',
                'valid_until' => '2026-11-01',
                'message' => 'Stock is available.',
            ]);
            $order = app(SupplierOrderService::class)->place($contractor, $price, $project->address, null);
            // Historical demo payment. Live orders stay pending until PayHere calls the same method.
            app(PaymentService::class)->confirmVerified($order->payment, 'demo-verified-flooring');
        }
    }

    private function tasks(Project $apartment, Project $coastal, User $contractor): void
    {
        $apartment->tasks()->updateOrCreate(
            ['name' => 'Kitchen Installation'],
            [
                'assignee_id' => $contractor->id,
                'assignee_label' => 'Construction Team',
                'category' => 'construction',
                'status' => ProjectTask::STATUS_IN_PROGRESS,
                'priority' => 'high',
                'started_on' => '2026-10-10',
                'due_on' => '2026-10-18',
                'progress' => 40,
                'notes' => 'Cabinets are on site. Worktops arrive next.',
            ],
        );
        $coastal->tasks()->updateOrCreate(
            ['name' => 'Electrical first fix'],
            [
                'assignee_id' => $contractor->id,
                'assignee_label' => 'Electrical Team',
                'category' => 'construction',
                'status' => 'pending',
                'priority' => 'normal',
                'due_on' => '2026-10-22',
                'progress' => 0,
            ],
        );
    }

    private function documents(Project $project, User $contractor): void
    {
        $path = 'projects/'.$project->id.'/documents/site-report.txt';
        Storage::disk('local')->put($path, 'Site report for '.$project->name);

        $project->documents()->updateOrCreate(
            ['name' => 'Site report'],
            [
                'uploaded_by' => $contractor->id,
                'description' => 'Weekly site report.',
                'original_name' => 'site-report.txt',
                'category' => 'construction',
                'disk' => 'local',
                'path' => $path,
                'mime' => 'text/plain',
                'size' => 48,
            ],
        );
    }

    private function change(Project $project, User $homeowner): void
    {
        $project->changeRequests()->updateOrCreate(
            ['title' => 'Extend the pool deck'],
            [
                'requested_by' => $homeowner->id,
                'description' => 'Extend the pool deck toward the garden.',
                'reason' => 'More outdoor seating.',
                'category' => 'construction',
                'priority' => 'normal',
                'status' => ChangeRequest::STATUS_SUBMITTED,
            ],
        );
    }

    private function messages(User $homeowner, ?User $designer, User $contractor, User $support, Project $project): void
    {
        $service = app(ConversationService::class);
        $this->thread($service, $homeowner, $contractor, $project, 'Can the kitchen installation start on the 10th?');
        if ($designer) {
            $this->thread($service, $homeowner, $designer, $project, 'The approved kitchen layout is in the documents.');
            $this->direct($contractor, $designer, $project, 'The site measurements are ready when you need them.');
        }
        $this->thread($service, $homeowner, $support, null, 'RenovaHub Support can help with payment questions.');
    }

    private function thread(ConversationService $service, User $homeowner, User $other, ?Project $project, string $body): void
    {
        $conversation = $service->findOrCreate($homeowner, $other, $project);
        if ($conversation->messages()->count() === 0) {
            $service->send($homeowner, $conversation, $body);
        }
    }

    private function direct(User $contractor, User $designer, Project $project, string $body): void
    {
        $existing = Conversation::query()
            ->where('project_id', $project->id)
            ->whereHas('participants', fn ($query) => $query->where('users.id', $contractor->id))
            ->whereHas('participants', fn ($query) => $query->where('users.id', $designer->id))
            ->first();

        if ($existing) {
            return;
        }

        $conversation = Conversation::query()->create([
            'project_id' => $project->id,
            'kind' => 'professional',
            'last_preview' => $body,
            'last_message_at' => now(),
        ]);
        $conversation->participants()->attach([$contractor->id, $designer->id]);
        $conversation->messages()->create(['sender_id' => $contractor->id, 'body' => $body]);
    }

    private function activity(Project $apartment, Project $coastal, User $homeowner, User $contractor): void
    {
        foreach ([
            [$apartment, $homeowner, 'quotation.approved', 'Homeowner approved quotation QTN-APT-01'],
            [$apartment, $contractor, 'supplier.order.placed', 'Supplier order placed for the kitchen cabinets'],
            [$apartment, $contractor, 'supplier.price.received', 'Supplier price received from ABC Interiors'],
            [$apartment, $homeowner, 'payment.received', 'Payment received for the oak flooring package'],
            [$coastal, $homeowner, 'change.submitted', 'Change request submitted to extend the pool deck'],
            [$apartment, $contractor, 'task.completed', 'Construction milestone completed for the demolition stage'],
        ] as [$project, $user, $event, $description]) {
            ActivityLog::query()->firstOrCreate(
                ['project_id' => $project->id, 'event' => $event, 'description' => $description],
                ['user_id' => $user->id],
            );
        }
    }

    private function portfolio(ProfessionalProfile $profile): void
    {
        $case = $profile->caseStudies()->updateOrCreate(
            ['slug' => 'colombo-apartment-makeover'],
            [
                'title' => 'Colombo Apartment Makeover',
                'location' => 'Colombo 07',
                'summary' => 'Kitchen, flooring and joinery for a completed apartment renovation.',
                'hero_image' => 'images/renova/about-interior.jpg',
                'sort_order' => 1,
            ],
        );

        if ($case->images()->count() < 5) {
            $case->images()->delete();
            foreach ([
                'images/renova/about-interior.jpg',
                'images/renova/feature-plans.jpg',
                'images/renova/feature-green.jpg',
                'images/renova/hero.jpg',
                'images/renova/about-exterior.jpg',
            ] as $index => $path) {
                $case->images()->create(['path' => $path, 'sort_order' => $index]);
            }
        }
    }

    private function materials(Project $project, DesignConcept $concept, int $count): void
    {
        $rows = [
            ['Porcelain Tile', 'Living Room', 'Flooring', 450, 'sq.ft', '600 × 600 mm'],
            ['Kitchen Cabinet', 'Kitchen', 'Joinery', 1, 'set', 'Warm oak, soft-close'],
            ['Countertop', 'Kitchen', 'Stone', 35, 'sq.ft', 'Quartz, 20 mm'],
            ['Bathroom Tile', 'Bathroom', 'Flooring', 180, 'sq.ft', '300 × 600 mm'],
            ['Lighting', 'Whole house', 'Lighting', 18, 'units', 'Warm white'],
            ['Doors', 'Bedrooms', 'Joinery', 6, 'nos', 'Solid timber'],
            ['Windows', 'Living Room', 'Glazing', 4, 'nos', 'Powder-coated aluminium'],
            ['Sanitaryware', 'Bathrooms', 'Plumbing', 3, 'sets', 'Wall-hung'],
            ['Flooring', 'Bedrooms', 'Flooring', 620, 'sq.ft', 'Engineered oak'],
            ['Paint', 'Whole house', 'Finishes', 48, 'litre', 'Low-VOC emulsion'],
            ['Furniture', 'Living Room', 'Furniture', 1, 'set', 'Sofa and side tables'],
            ['Wardrobes', 'Bedrooms', 'Joinery', 3, 'nos', 'Floor to ceiling'],
            ['Vanity', 'Bathroom', 'Joinery', 2, 'nos', 'Timber and stone'],
            ['Switches', 'Whole house', 'Electrical', 42, 'nos', 'Brushed brass'],
            ['Curtains', 'Bedrooms', 'Soft furnishings', 8, 'nos', 'Linen'],
            ['Outdoor tile', 'Terrace', 'Flooring', 220, 'sq.ft', 'Slip-resistant'],
            ['Skirting', 'Whole house', 'Joinery', 240, 'm', 'Matching timber'],
            ['Ceiling feature', 'Living Room', 'Finishes', 1, 'item', 'Timber slats'],
        ];

        foreach (array_slice($rows, 0, $count) as [$name, $room, $category, $qty, $unit, $spec]) {
            MaterialRequirement::query()->updateOrCreate(
                ['project_id' => $project->id, 'name' => $name],
                [
                    'design_concept_id' => $concept->id,
                    'room' => $room,
                    'category' => $category,
                    'quantity' => $qty,
                    'unit' => $unit,
                    'specification' => $spec,
                    'status' => 'pending',
                ],
            );
        }
    }

    private function coverFor(string $name): string
    {
        return $this->galleryFor($name)[0];
    }

    /**
     * @return list<string>
     */
    private function galleryFor(string $name): array
    {
        $pool = [
            'images/projects/apartment.jpg',
            'images/projects/coastal-villa.jpg',
            'images/projects/garden-house.jpg',
            'images/projects/modern-house.jpg',
            'images/projects/beach-house.jpg',
            'images/projects/bungalow.jpg',
            'images/projects/living.jpg',
            'images/projects/kitchen.jpg',
            'images/projects/bathroom.jpg',
            'images/projects/render.jpg',
        ];
        $start = match (true) {
            str_contains($name, 'Apartment') => 0,
            str_contains($name, 'Coastal') => 1,
            str_contains($name, 'Green') => 2,
            str_contains($name, 'Modern') => 3,
            str_contains($name, 'Beach') => 4,
            default => 5,
        };
        $photos = [];
        for ($i = 0; $i < 5; $i++) {
            $photos[] = $pool[($start + ($i * 2)) % count($pool)];
        }

        return $photos;
    }

    private function notify(User $contractor, Project $project): void
    {
        if ($contractor->notifications()->where('data->title', 'New project offer')->exists()) {
            return;
        }

        app(NotificationService::class)->notify(
            $contractor,
            'New project offer',
            'A homeowner invited you to Modern House Renovation.',
            'projects',
            route('contractor.invitations.index'),
        );
    }

    /**
     * Mixed paid and pending shares so the earnings and payments screens have real records.
     */
    private function showcasePayments(User $homeowner, ?User $designer, User $contractor, Project $apartment, Project $coastal, Project $valley): void
    {
        $lanka = Supplier::query()->where('slug', 'lanka-hardware')->first();
        $abc = Supplier::query()->where('slug', 'abc-interiors')->first();
        $urban = ConstructionFirm::query()->where('slug', 'urban-builders')->first();
        $buildRight = ConstructionFirm::query()->where('slug', 'buildright-construction')->first();
        $prime = ConstructionFirm::query()->where('slug', 'prime-structures')->first();

        $this->writeShowcase($homeowner, $designer, $contractor, $apartment, [
            'project_amount' => 4250000,
            'designer_fee' => 250000,
            'materials' => 425000,
            'construction' => 1650000,
            'contractor_fee' => 500000,
            'changes' => 1425000,
            'homeowner_paid' => false,
            'supplier_paid' => true,
            'construction_paid' => false,
            'supplier_id' => $lanka?->id,
            'supplier_name' => $lanka?->name ?: 'Lanka Hardware',
            'material' => 'Porcelain Floor Tiles',
            'firm_id' => $urban?->id,
            'firm_name' => $urban?->name ?: 'Urban Builders',
            'scope' => 'Construction and Labour',
        ]);

        $this->writeShowcase($homeowner, $designer, $contractor, $coastal, [
            'project_amount' => 5800000,
            'designer_fee' => 250000,
            'materials' => 680000,
            'construction' => 2400000,
            'contractor_fee' => 600000,
            'changes' => 1870000,
            'homeowner_paid' => true,
            'supplier_paid' => true,
            'construction_paid' => false,
            'supplier_id' => $lanka?->id,
            'supplier_name' => $lanka?->name ?: 'Lanka Hardware',
            'material' => 'Bathroom Stone Tiles',
            'firm_id' => $buildRight?->id,
            'firm_name' => $buildRight?->name ?: 'BuildRight Construction',
            'scope' => 'Pool deck and bathroom construction',
        ]);

        $this->writeShowcase($homeowner, $designer, $contractor, $valley, [
            'project_amount' => 6200000,
            'designer_fee' => 180000,
            'materials' => 900000,
            'construction' => 2100000,
            'contractor_fee' => 450000,
            'changes' => 2570000,
            'homeowner_paid' => true,
            'supplier_paid' => false,
            'construction_paid' => true,
            'supplier_id' => $abc?->id,
            'supplier_name' => $abc?->name ?: 'ABC Interiors',
            'material' => 'Timber Flooring',
            'firm_id' => $prime?->id,
            'firm_name' => $prime?->name ?: 'Prime Structures',
            'scope' => 'Construction and Labour',
        ]);
    }

    /**
     * @param  array<string, mixed>  $figures
     */
    private function writeShowcase(User $homeowner, ?User $designer, User $contractor, Project $project, array $figures): void
    {
        $percent = (float) config('renovahub.platform_fee_percent', 2);
        $fee = round($figures['project_amount'] * ($percent / 100), 2);
        $total = round($figures['project_amount'] + $fee, 2);
        $service = app(PaymentService::class);

        $submission = $project->budgetSubmissions()->latest('id')->first();
        $payload = [
            'contractor_id' => $contractor->id,
            'designer_fee' => $figures['designer_fee'],
            'materials' => $figures['materials'],
            'construction' => $figures['construction'],
            'contractor_fee' => $figures['contractor_fee'],
            'changes' => $figures['changes'],
            'platform_fee' => $fee,
            'fee_percent' => $percent,
            'total' => $total,
            'status' => BudgetSubmission::STATUS_APPROVED,
            'submitted_at' => $submission?->submitted_at ?? now()->subDays(4),
            'decided_at' => now()->subDays(2),
        ];

        if ($submission) {
            $submission->update($payload);
        } else {
            $submission = $project->budgetSubmissions()->create($payload);
        }

        $payment = Payment::query()->where('budget_submission_id', $submission->id)->first();

        if ($payment === null && $submission->payment_id) {
            $payment = Payment::query()->find($submission->payment_id);
        }

        $paymentData = [
            'budget_submission_id' => $submission->id,
            'payer_id' => $homeowner->id,
            'payee_id' => $contractor->id,
            'amount' => $total,
            'renovation_amount' => $figures['project_amount'],
            'platform_fee' => $fee,
            'net_amount' => $total,
            'fee_percent' => $percent,
            'currency' => 'LKR',
            'provider' => 'payhere',
            'status' => $figures['homeowner_paid'] ? Payment::STATUS_PAID : Payment::STATUS_PENDING,
            'method' => $figures['homeowner_paid'] ? 'payhere' : null,
            'paid_at' => $figures['homeowner_paid'] ? now()->subDays(2) : null,
            'notes' => 'Final project payment for '.$project->name.'.',
        ];

        if ($payment) {
            $paymentData['provider_reference'] = $figures['homeowner_paid']
                ? ($payment->provider_reference ?: 'demo-'.$payment->reference)
                : null;
            $payment->update($paymentData);
        } else {
            $reference = $service->nextReference($project);
            $payment = $project->payments()->create($paymentData + [
                'reference' => $reference,
                'provider_reference' => $figures['homeowner_paid'] ? 'demo-'.$reference : null,
            ]);
        }

        $submission->update(['payment_id' => $payment->id]);
        $service->ensureDisbursements($payment->fresh());

        $this->setShare($payment, $project, $service, PaymentAllocation::MATERIALS, [
            'supplier_id' => $figures['supplier_id'],
            'label' => $figures['supplier_name'],
            'detail' => $figures['material'],
            'amount' => $figures['materials'],
            'platform_fee' => 0,
            'net_amount' => $figures['materials'],
            'paid' => $figures['supplier_paid'],
            'payer_id' => $figures['supplier_paid'] ? $contractor->id : null,
        ]);
        $this->setShare($payment, $project, $service, PaymentAllocation::CONSTRUCTION, [
            'construction_firm_id' => $figures['firm_id'],
            'label' => $figures['firm_name'],
            'detail' => $figures['scope'],
            'amount' => $figures['construction'],
            'platform_fee' => 0,
            'net_amount' => $figures['construction'],
            'paid' => $figures['construction_paid'],
            'payer_id' => $figures['construction_paid'] ? $contractor->id : null,
        ]);

        $contractorFee = ContractorEarning::feeFor((float) $figures['contractor_fee'], $percent);
        $contractorNet = ContractorEarning::netFor((float) $figures['contractor_fee'], $percent);
        $contractorShare = $this->setShare($payment, $project, $service, PaymentAllocation::CONTRACTOR, [
            'payee_user_id' => $contractor->id,
            'label' => $contractor->name,
            'detail' => 'Contractor',
            'amount' => $figures['contractor_fee'],
            'platform_fee' => $contractorFee,
            'net_amount' => $contractorNet,
            'paid' => true,
            'payer_id' => $homeowner->id,
            'provider' => 'payhere',
        ]);

        if ($designer) {
            $designerFee = DesignerEarning::feeFor((float) $figures['designer_fee']);
            $this->setShare($payment, $project, $service, PaymentAllocation::DESIGNER, [
                'payee_user_id' => $designer->id,
                'label' => $designer->name,
                'detail' => 'Designer',
                'amount' => $figures['designer_fee'],
                'platform_fee' => $designerFee,
                'net_amount' => DesignerEarning::netFor((float) $figures['designer_fee']),
                'paid' => (bool) $figures['homeowner_paid'],
                'payer_id' => $figures['homeowner_paid'] ? $homeowner->id : null,
                'provider' => 'payhere',
            ]);
        }

        ContractorEarning::query()->updateOrCreate(
            ['payment_id' => $payment->id, 'contractor_id' => $contractor->id],
            [
                'project_id' => $project->id,
                'label' => 'Contractor fee · '.$project->name,
                'gross_amount' => $figures['contractor_fee'],
                'fee_percent' => $percent,
                'fee_amount' => $contractorFee,
                'net_amount' => $contractorNet,
                'status' => ContractorEarning::STATUS_RECORDED,
                'reference' => $contractorShare->reference,
                'recorded_on' => now()->subDays(2)->toDateString(),
                'notes' => 'RenovaHub service charge on the contractor portion of the project payment.',
            ],
        );

        if ($designer && $figures['homeowner_paid']) {
            $existing = DesignerEarning::query()
                ->where('project_id', $project->id)
                ->where('designer_id', $designer->id)
                ->where('label', 'Designer fee · '.$project->name)
                ->first();

            DesignerEarning::query()->updateOrCreate(
                ['reference' => $existing?->reference ?? $service->nextReference($project)],
                [
                    'project_id' => $project->id,
                    'designer_id' => $designer->id,
                    'label' => 'Designer fee · '.$project->name,
                    'gross_amount' => $figures['designer_fee'],
                    'fee_percent' => DesignerEarning::FEE_PERCENT,
                    'fee_amount' => DesignerEarning::feeFor((float) $figures['designer_fee']),
                    'net_amount' => DesignerEarning::netFor((float) $figures['designer_fee']),
                    'status' => DesignerEarning::STATUS_RECORDED,
                    'recorded_on' => now()->subDays(2)->toDateString(),
                    'notes' => 'RenovaHub service charge on the designer portion of the verified project payment.',
                ],
            );
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function setShare(Payment $payment, Project $project, PaymentService $service, string $bucket, array $data): PaymentAllocation
    {
        $allocation = $payment->allocations()->where('bucket', $bucket)->first();
        $paid = (bool) $data['paid'];
        $fields = [
            'project_id' => $project->id,
            'amount' => $data['amount'],
            'status' => $paid ? PaymentAllocation::STATUS_PAID : PaymentAllocation::STATUS_PENDING,
            'label' => $data['label'],
            'detail' => $data['detail'],
            'platform_fee' => $data['platform_fee'],
            'net_amount' => $data['net_amount'],
            'supplier_id' => $data['supplier_id'] ?? null,
            'construction_firm_id' => $data['construction_firm_id'] ?? null,
            'payee_user_id' => $data['payee_user_id'] ?? null,
            'payer_id' => $data['payer_id'] ?? null,
            'paid_at' => $paid ? ($allocation?->paid_at ?? now()->subDay()) : null,
            'provider' => $paid ? ($data['provider'] ?? 'renovahub') : null,
            'provider_reference' => $paid ? ($allocation?->provider_reference ?: 'demo-'.$bucket.'-'.$project->id) : null,
        ];

        if ($allocation) {
            if (! $allocation->reference) {
                $fields['reference'] = $service->nextReference($project);
            }

            $allocation->update($fields);

            return $allocation->fresh();
        }

        return $payment->allocations()->create($fields + [
            'bucket' => $bucket,
            'reference' => $service->nextReference($project),
        ]);
    }
}
