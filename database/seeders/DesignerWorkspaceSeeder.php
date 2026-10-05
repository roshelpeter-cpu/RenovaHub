<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Conversation;
use App\Models\DesignChangeRequest;
use App\Models\DesignConcept;
use App\Models\DesignerEarning;
use App\Models\DesignTask;
use App\Models\MoodBoard;
use App\Models\Project;
use App\Models\ProjectInvitation;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

/**
 * Emma Fernando's designer workspace.
 * These projects belong to a separate homeowner so Roshel's approved pages stay unchanged.
 * The password is hashed here and is never printed in a view.
 */
class DesignerWorkspaceSeeder extends Seeder
{
    public function run(): void
    {
        $emma = User::query()->updateOrCreate(
            ['email' => 'designer@test.com'],
            [
                'name' => 'Emma Fernando',
                'password' => Hash::make('designer@123'),
                'role' => 'designer',
                'phone' => '0775550142',
                'address' => '18 Barnes Place, Colombo 07',
                'email_verified_at' => now(),
            ],
        );

        $profile = $emma->professionalProfile()->updateOrCreate(
            ['user_id' => $emma->id],
            [
                'professional_type' => 'designer',
                'title' => 'Interior Designer',
                'specialization' => 'Residential interiors, mood boards, material palettes',
                'bio' => 'Emma designs calm, lived-in homes with warm timber, soft colour and practical storage.',
                'about' => 'She leads the design direction from the first brief through the final package, and leaves construction, quotations and payments with the contractor and homeowner.',
                'location' => 'Colombo, Sri Lanka',
                'avatar_path' => 'images/renova/feature-collab.jpg',
                'years_experience' => 9,
                'completed_projects_count' => 24,
                'rating' => 4.8,
                'starting_price' => 250000,
                'listed' => true,
            ],
        );

        $nadee = User::query()->updateOrCreate(
            ['email' => 'nadee.silva@renovahub.test'],
            [
                'name' => 'Nadee Silva',
                'password' => Hash::make('homeowner-demo-only'),
                'role' => 'homeowner',
                'email_verified_at' => now(),
            ],
        );

        // Kavinda Perera is a designer in the professional directory. The project
        // contractor has to be a contractor account so design and construction stay separate.
        $contractor = User::query()->where('role', 'contractor')->where('email', 'abc.renovations@renovahub.test')->first()
            ?? User::query()->where('role', 'contractor')->first();

        $resetIds = $nadee->projects()->whereIn('name', [
            'Modern House Renovation',
            'Luxury Apartment Design',
            'Apartment Interior Makeover',
            'Coastal Villa Renovation',
            'Green Valley Residence',
        ])->pluck('id');

        // Project deletion nulls the conversation link, so remove these demo
        // threads first. Otherwise a second seed leaves duplicate chats.
        if ($resetIds->isNotEmpty()) {
            Conversation::query()->whereIn('project_id', $resetIds)->delete();
        }

        $nadee->projects()->whereIn('id', $resetIds)->delete();

        $photos = [
            'images/renova/about-interior.jpg',
            'images/renova/feature-plans.jpg',
            'images/renova/feature-green.jpg',
            'images/renova/feature-collab.jpg',
            'images/renova/about-exterior.jpg',
        ];

        $accepted = [
            $this->project($nadee, $emma, $contractor, 'Apartment Interior Makeover', 'Colombo 07', 'Apartment', 'apartment', 'A modern interior makeover focusing on open-plan living, a contemporary kitchen and custom storage.', 65, 'Mood Board', 'Complete Kitchen Render', '2026-10-10', $photos, true),
            $this->project($nadee, $emma, $contractor, 'Coastal Villa Renovation', 'Galle', 'Villa', 'villa', 'A coastal villa renovation with open rooms, natural textures and a calm tropical palette.', 40, 'Design Concepts', 'Prepare Final Design Package', '2026-10-20', array_reverse($photos), true),
            $this->project($nadee, $emma, $contractor, 'Green Valley Residence', 'Colombo', 'House', 'house', 'A family residence being opened toward the garden with warm timber and soft sage walls.', 80, 'Final Design', 'Homeowner review', '2026-11-02', $photos, true),
        ];

        $this->project($nadee, null, null, 'Modern House Renovation', 'Colombo', 'House', 'house', 'Renovation of a detached house with open-plan living, a modern kitchen and outdoor area.', 0, 'Design Brief', 'Awaiting designer', '2027-07-01', $photos, false, 8500000, '2027-01-15', '2027-07-30');
        $this->project($nadee, null, null, 'Luxury Apartment Design', 'Colombo 03', 'Apartment', 'apartment', 'Full interior design for a luxury apartment, focused on warm timber and indoor planting.', 0, 'Design Brief', 'Awaiting designer', '2027-05-01', array_reverse($photos), false, 12000000, '2026-11-01', '2027-05-20');

        foreach (['Modern House Renovation', 'Luxury Apartment Design'] as $name) {
            $offer = $nadee->projects()->where('name', $name)->firstOrFail();
            $offer->invitations()->create([
                'user_id' => $emma->id,
                'role' => 'designer',
                'status' => ProjectInvitation::STATUS_PENDING,
            ]);
        }

        $apartment = $accepted[0];
        $coastal = $accepted[1];
        $valley = $accepted[2];

        $this->board($apartment, $emma, MoodBoard::STATUS_REVISION, 'Please warm the kitchen cabinets and soften the bedroom wall colour.', [
            ['inspiration', 'Living room daylight', null, 'images/renova/about-interior.jpg'],
            ['colour', 'Warm Beige', '#F3EFE7', null],
            ['colour', 'Sage Green', '#A7B89F', null],
            ['material', 'Light Oak', '#C4A574', null],
            ['furniture', 'Sofa', null, 'images/renova/feature-collab.jpg'],
            ['note', 'Kitchen', 'Keep the cabinets lighter until the homeowner confirms walnut.', null],
        ]);
        $this->board($coastal, $emma, MoodBoard::STATUS_AWAITING, null, [
            ['inspiration', 'Pool edge', null, 'images/renova/about-exterior.jpg'],
            ['colour', 'Sand White', '#F8F6F1', null],
            ['material', 'Limestone', '#D7C9B1', null],
            ['furniture', 'Lounge Chair', null, 'images/renova/feature-green.jpg'],
            ['note', 'Outdoor rooms', 'Shade the west terrace before adding dark timber.', null],
        ]);
        $this->board($valley, $emma, MoodBoard::STATUS_APPROVED, null, [
            ['inspiration', 'Garden living', null, 'images/renova/feature-green.jpg'],
            ['colour', 'Warm White', '#F8F6F1', null],
            ['material', 'Oak Wood', '#8A7A5B', null],
            ['furniture', 'Dining Table', null, 'images/renova/feature-plans.jpg'],
            ['note', 'Final direction', 'Approved palette. Do not replace the oak.', null],
        ]);

        $this->concept($apartment, $emma, 'Concept 01 — Modern Minimal', DesignConcept::STATUS_AWAITING, 'A quiet open kitchen with light oak and linen.');
        $this->concept($apartment, $emma, 'Concept 02 — Warm Contemporary', DesignConcept::STATUS_DRAFT, 'A warmer timber kitchen prepared after the revision note.');
        $this->concept($coastal, $emma, 'Concept 01 — Tropical Light', DesignConcept::STATUS_AWAITING, 'Limestone floors and a shaded terrace.');
        $this->concept($valley, $emma, 'Concept 01 — Garden House', DesignConcept::STATUS_APPROVED, 'The approved final living and dining concept.');
        $this->concept($valley, $emma, 'Concept 03 — Evening Lighting', DesignConcept::STATUS_AWAITING, 'Pendant and wall lighting for the approved palette.');

        $apartment->designChangeRequests()->create([
            'requested_by' => $nadee->id,
            'title' => 'Change kitchen cabinets to darker walnut',
            'description' => 'Change kitchen cabinets to darker walnut.',
            'design_impact' => 'Joinery and the approved material palette',
            'status' => DesignChangeRequest::STATUS_PENDING,
        ]);
        $coastal->designChangeRequests()->create([
            'requested_by' => $nadee->id,
            'title' => 'Soften the bedroom wall colour',
            'description' => 'The bedroom wall reads colder than the rest of the house.',
            'design_impact' => 'Paint schedule',
            'status' => DesignChangeRequest::STATUS_PENDING,
        ]);

        $this->task($apartment, $emma, 'Complete Kitchen Render', 'in_progress', 70, '2026-10-10');
        $this->task($apartment, $emma, 'Revise Bedroom Layout', 'pending', 0, '2026-10-15');
        $this->task($coastal, $emma, 'Prepare Final Design Package', 'pending', 0, '2026-10-20');
        $this->task($valley, $emma, 'Upload Final Design', 'in_progress', 80, '2026-11-02');

        foreach ([
            [$apartment, 'Living Room Concept', 'design', 'Living room concept set.'],
            [$apartment, 'Kitchen Layout', 'floor_plans', 'Kitchen layout drawing.'],
            [$coastal, 'Material Selection', 'materials', 'Stone and timber selection.'],
            [$valley, 'Final Design Package', 'final_design', 'Approved design package.'],
        ] as [$project, $name, $category, $description]) {
            $path = 'projects/'.$project->id.'/documents/'.str($name)->slug().'.txt';
            Storage::disk('local')->put($path, $description);
            $project->documents()->create([
                'uploaded_by' => $emma->id,
                'name' => $name,
                'description' => $description,
                'original_name' => str($name)->slug().'.txt',
                'category' => $category,
                'disk' => 'local',
                'path' => $path,
                'mime' => 'text/plain',
                'size' => strlen($description),
            ]);
        }

        $this->earning($apartment, $emma, 'Payment 01', 485000, DesignerEarning::STATUS_RECORDED, 'RH-DSN-00041', '2026-10-01');
        $this->earning($valley, $emma, 'Payment 01', 1200000, DesignerEarning::STATUS_RECORDED, 'RH-DSN-00018', '2026-08-12');
        $this->earning($coastal, $emma, 'Payment 01', 765000, DesignerEarning::STATUS_RECORDED, 'RH-DSN-00027', '2026-09-04');
        $this->earning($coastal, $emma, 'Payment 02', 250000, DesignerEarning::STATUS_PENDING, 'RH-DSN-00052', now()->toDateString());

        $this->thread($apartment, $emma, $nadee, 'Can we make the kitchen cabinets warmer?');
        if ($contractor) {
            $this->thread($coastal, $emma, $contractor, 'The site measurements are ready when you need them.');
        }
        $support = User::query()->where('email', 'support@renovahub.test')->first();
        if ($support) {
            $this->thread($valley, $emma, $support, 'Your design package is ready to share with the homeowner.', 'support');
        }

        $valley->feedback()->create([
            'homeowner_id' => $nadee->id,
            'professional_id' => $emma->id,
            'role' => 'designer',
            'rating' => 5,
            'title' => 'Excellent communication and beautiful design work.',
            'comment' => 'Excellent communication and beautiful design work.',
        ]);

        $profile->caseStudies()->delete();
        foreach ([
            ['Garden Living Room', 'Living room', 'Colombo', 'A light living room with timber, linen and a view to the garden.'],
            ['Coastal Bedroom', 'Bedroom', 'Galle', 'A quiet bedroom with limestone, oak and shaded daylight.'],
        ] as $index => [$title, $type, $location, $summary]) {
            $case = $profile->caseStudies()->create([
                'slug' => str($title)->slug().'-emma',
                'title' => $title,
                'project_type' => $type,
                'category' => $type,
                'location' => $location,
                'summary' => $summary,
                'overview' => $summary.' The gallery shows the concept, materials and the finished room from several angles.',
                'hero_image' => $photos[$index],
                'sort_order' => $index,
            ]);
            foreach ($photos as $order => $path) {
                $case->images()->create([
                    'path' => $path,
                    'caption' => $title,
                    'image_type' => 'gallery',
                    'sort_order' => $order,
                ]);
            }
        }

        $emma->notifications()->delete();

        foreach ([
            ['New project offer', 'Nadee Silva invited you to Modern House Renovation.', 'projects', route('designer.invitations.index')],
            ['Mood board revision requested', 'Apartment Interior Makeover needs a warmer kitchen.', 'design', route('designer.projects.mood-board', $apartment)],
            ['Design change request', 'Change kitchen cabinets to darker walnut.', 'design', route('designer.revisions.index')],
            ['Earnings updated', 'A professional earning was recorded. PayHere has not processed it.', 'payments', route('designer.earnings.index')],
        ] as [$title, $body, $category, $url]) {
            app(NotificationService::class)->notify($emma, $title, $body, $category, $url);
        }

        ActivityLog::query()->create(['project_id' => $apartment->id, 'user_id' => $nadee->id, 'event' => 'moodboard.revision', 'description' => 'Homeowner requested a mood board revision']);
        ActivityLog::query()->create(['project_id' => $coastal->id, 'user_id' => $emma->id, 'event' => 'moodboard.submitted', 'description' => 'Mood board submitted for approval']);
        ActivityLog::query()->create(['project_id' => $valley->id, 'user_id' => $nadee->id, 'event' => 'moodboard.approved', 'description' => 'Homeowner approved mood board']);
    }

    private function project(User $owner, ?User $designer, ?User $contractor, string $name, string $city, string $typeLabel, string $property, string $description, int $designProgress, string $stage, string $next, string $due, array $photos, bool $accepted, float $budget = 12500000, string $start = '2026-01-12', string $end = '2026-12-15'): Project
    {
        $project = $owner->projects()->create([
            'name' => $name,
            'description' => $description,
            'requirements' => $description,
            'renovation_type' => 'interior',
            'property_type' => $property,
            'size_sq_ft' => 1800,
            'address' => '12 Design Lane',
            'city' => $city,
            'province' => $city === 'Galle' ? 'Southern Province' : 'Western Province',
            'estimated_budget' => $budget,
            'current_budget' => $budget,
            'currency' => 'LKR',
            'expected_start_date' => $start,
            'expected_completion_date' => $end,
            'status' => $accepted ? Project::STATUS_IN_PROGRESS : Project::STATUS_AWAITING_TEAM,
            'progress' => $designProgress,
            'cover_image' => match (true) {
                str_contains($name, 'Apartment') => 'images/projects/apartment.jpg',
                str_contains($name, 'Coastal') => 'images/projects/coastal-villa.jpg',
                str_contains($name, 'Green Valley') => 'images/projects/garden-house.jpg',
                str_contains($name, 'Modern') => 'images/projects/modern-house.jpg',
                str_contains($name, 'Luxury') => 'images/projects/living.jpg',
                default => 'images/projects/render.jpg',
            },
            'designer_id' => $accepted ? $designer?->id : null,
            'contractor_id' => $accepted ? $contractor?->id : null,
            'workspace_meta' => [
                'type_label' => $typeLabel,
                'design_progress' => $designProgress,
                'design_stage' => $stage,
                'next_milestone' => $next,
                'design_due' => $due,
                'brief' => [
                    'rooms' => 'Living, kitchen, bedrooms and bathrooms',
                    'style' => 'Warm contemporary',
                    'colours' => 'Warm beige, sage and timber',
                    'functional' => 'Storage, daylight and a kitchen that can host family meals',
                    'notes' => 'Use the reference images as a starting point, not a copy.',
                ],
            ],
        ]);

        $gallery = [
            'images/projects/apartment.jpg',
            'images/projects/living.jpg',
            'images/projects/kitchen.jpg',
            'images/projects/bathroom.jpg',
            'images/projects/render.jpg',
            'images/projects/coastal-villa.jpg',
            'images/projects/garden-house.jpg',
            'images/projects/modern-house.jpg',
        ];
        $offset = match (true) {
            str_contains($name, 'Coastal') => 5,
            str_contains($name, 'Green') => 6,
            str_contains($name, 'Modern') => 7,
            str_contains($name, 'Luxury') => 1,
            default => 0,
        };
        foreach (array_slice(array_merge(array_slice($gallery, $offset), array_slice($gallery, 0, $offset)), 0, 5) as $path) {
            $project->referenceImages()->create(['path' => $path, 'original_name' => basename($path)]);
        }

        if ($accepted && $designer) {
            $project->invitations()->create([
                'user_id' => $designer->id,
                'role' => 'designer',
                'status' => ProjectInvitation::STATUS_ACCEPTED,
                'responded_at' => now()->subMonth(),
            ]);
        }

        if ($accepted && $contractor) {
            $project->invitations()->create([
                'user_id' => $contractor->id,
                'role' => 'contractor',
                'status' => ProjectInvitation::STATUS_ACCEPTED,
                'responded_at' => now()->subMonth(),
            ]);
        }

        return $project;
    }

    private function board(Project $project, User $designer, string $status, ?string $note, array $items): void
    {
        $board = $project->moodBoard()->create([
            'created_by' => $designer->id,
            'title' => $project->name.' Mood Board',
            'summary' => 'Warm timber, soft colour and daylight.',
            'status' => $status,
            'revision_note' => $note,
            'submitted_at' => $status === MoodBoard::STATUS_DRAFT ? null : now()->subDays(4),
            'approved_at' => $status === MoodBoard::STATUS_APPROVED ? now()->subDays(2) : null,
            'version' => $status === MoodBoard::STATUS_REVISION ? 2 : 1,
        ]);

        foreach ($items as [$kind, $title, $colour, $image]) {
            $board->items()->create([
                'kind' => $kind,
                'title' => $title,
                'body' => $kind === 'note' ? $colour : null,
                'colour' => $kind === 'note' ? null : $colour,
                'image' => $image,
            ]);
        }
    }

    private function concept(Project $project, User $designer, string $title, string $status, string $description): void
    {
        $concept = $project->designConcepts()->create([
            'designer_id' => $designer->id,
            'title' => $title,
            'description' => $description,
            'notes' => 'Materials follow the current mood board.',
            'status' => $status,
            'submitted_at' => $status === DesignConcept::STATUS_DRAFT ? null : now()->subDays(3),
            'approved_at' => $status === DesignConcept::STATUS_APPROVED ? now()->subDay() : null,
        ]);

        foreach (['images/renova/about-interior.jpg', 'images/renova/feature-plans.jpg', 'images/renova/feature-green.jpg'] as $path) {
            $concept->files()->create(['kind' => 'render', 'path' => $path, 'caption' => $title]);
        }
    }

    private function task(Project $project, User $designer, string $name, string $status, int $progress, string $due): void
    {
        DesignTask::query()->create([
            'project_id' => $project->id,
            'designer_id' => $designer->id,
            'name' => $name,
            'category' => 'design',
            'status' => $status,
            'progress' => $progress,
            'due_on' => $due,
        ]);
    }

    private function earning(Project $project, User $designer, string $label, float $gross, string $status, string $reference, string $date): void
    {
        DesignerEarning::query()->create([
            'project_id' => $project->id,
            'designer_id' => $designer->id,
            'label' => $label,
            'gross_amount' => $gross,
            'fee_percent' => DesignerEarning::FEE_PERCENT,
            'fee_amount' => DesignerEarning::feeFor($gross),
            'net_amount' => DesignerEarning::netFor($gross),
            'status' => $status,
            'reference' => $reference,
            'recorded_on' => $date,
            'notes' => 'Demo record. PayHere checkout is not connected, so this is not an external payment.',
        ]);
    }

    private function thread(Project $project, User $emma, User $other, string $preview, string $kind = 'professional'): void
    {
        $existing = Conversation::query()
            ->where('project_id', $project->id)
            ->where('kind', $kind)
            ->whereHas('participants', fn ($query) => $query->where('users.id', $emma->id))
            ->whereHas('participants', fn ($query) => $query->where('users.id', $other->id))
            ->first();

        if ($existing) {
            return;
        }

        $conversation = Conversation::query()->create([
            'project_id' => $project->id,
            'kind' => $kind,
            'last_preview' => $preview,
            'last_message_at' => now()->subHours(2),
        ]);
        $conversation->participants()->attach([$emma->id, $other->id]);
        $conversation->messages()->create([
            'sender_id' => $other->id,
            'body' => $preview,
        ]);
        $conversation->messages()->create([
            'sender_id' => $emma->id,
            'body' => 'I will update the design and send it through the project.',
        ]);
    }
}
