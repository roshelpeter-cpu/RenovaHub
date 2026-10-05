<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\ChangeRequest;
use App\Models\Conversation;
use App\Models\Document;
use App\Models\ProfessionalProfile;
use App\Models\Project;
use App\Models\ProjectInvitation;
use App\Models\ProjectTask;
use App\Models\Quotation;
use App\Models\Supplier;
use App\Models\SupplierCategory;
use App\Models\SupplierOrder;
use App\Models\SupplierPriceRequest;
use App\Models\User;
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
        $this->portfolio($profile);
        $this->notify($kavinda, $apartment);
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
            'cover_image' => $photos[0],
            'designer_id' => $designer?->id,
            'contractor_id' => $accepted ? $contractor?->id : null,
            'workspace_meta' => [
                'type_label' => $typeLabel,
                'next_milestone' => $milestone,
                'milestone_due' => $due,
            ],
        ]);
        $project->save();

        if ($project->referenceImages()->count() < 5) {
            $project->referenceImages()->delete();
            foreach ($photos as $path) {
                $project->referenceImages()->create(['path' => $path, 'original_name' => basename($path)]);
            }
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
}
