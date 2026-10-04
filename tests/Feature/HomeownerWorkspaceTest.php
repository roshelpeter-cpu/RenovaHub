<?php

namespace Tests\Feature;

use App\Models\ChangeRequest;
use App\Models\Conversation;
use App\Models\Payment;
use App\Models\Project;
use App\Models\ProjectInvitation;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class HomeownerWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_budget_must_be_positive_and_completion_after_start(): void
    {
        $homeowner = User::factory()->create(['role' => 'homeowner']);
        $project = Project::factory()->for($homeowner, 'homeowner')->create();

        $this->actingAs($homeowner)
            ->from(route('homeowner.projects.budget', $project))
            ->put(route('homeowner.projects.budget.update', $project), [
                'estimated_budget' => 0,
                'expected_start_date' => '2026-05-01',
                'expected_completion_date' => '2026-04-01',
            ])
            ->assertRedirect(route('homeowner.projects.budget', $project))
            ->assertSessionHasErrors(['estimated_budget', 'expected_completion_date']);
    }

    public function test_invitation_acceptance_confirms_the_project_and_decline_can_be_replaced(): void
    {
        $homeowner = User::factory()->create(['role' => 'homeowner']);
        $designer = User::factory()->create(['role' => 'designer']);
        $replacement = User::factory()->create(['role' => 'designer']);
        $project = Project::factory()->for($homeowner, 'homeowner')->create([
            'designer_id' => $designer->id,
            'status' => Project::STATUS_AWAITING_TEAM,
        ]);
        $invitation = $project->invitations()->create([
            'user_id' => $designer->id,
            'role' => 'designer',
            'status' => ProjectInvitation::STATUS_PENDING,
        ]);

        $this->actingAs($designer)
            ->post(route('invitations.respond', $invitation), ['decision' => 'accepted'])
            ->assertRedirect(route('dashboard'));

        $this->assertSame(ProjectInvitation::STATUS_ACCEPTED, $invitation->fresh()->status);

        $this->actingAs($homeowner)
            ->put(route('homeowner.projects.team.update', $project), [
                'designer_id' => $replacement->id,
                'contractor_id' => null,
            ])
            ->assertRedirect();

        $this->assertNotNull($project->invitations()->where('user_id', $replacement->id)->where('status', 'pending')->first());
    }

    public function test_workspace_records_are_limited_to_the_owning_homeowner(): void
    {
        Storage::fake('local');
        $owner = User::factory()->create(['role' => 'homeowner']);
        $other = User::factory()->create(['role' => 'homeowner']);
        $project = Project::factory()->for($owner, 'homeowner')->create();
        $quotation = $project->quotations()->create([
            'number' => 'Q-1',
            'description' => 'Kitchen package',
            'materials' => 100,
            'labour' => 50,
            'additional_costs' => 0,
            'discount' => 0,
            'subtotal' => 150,
            'total' => 150,
            'status' => Quotation::STATUS_PENDING,
        ]);
        $change = $project->changeRequests()->create([
            'requested_by' => $owner->id,
            'title' => 'Darker stone',
            'description' => 'Use dark granite.',
            'category' => 'material',
            'priority' => 'normal',
            'status' => ChangeRequest::STATUS_SUBMITTED,
        ]);
        $document = $project->documents()->create([
            'uploaded_by' => $owner->id,
            'name' => 'Plan',
            'original_name' => 'plan.pdf',
            'category' => 'design',
            'disk' => 'local',
            'path' => 'projects/plan.pdf',
            'size' => 10,
        ]);
        Storage::disk('local')->put('projects/plan.pdf', 'plan');
        $project->messages()->create(['sender_id' => $owner->id, 'body' => 'Hello', 'read_at' => now()]);
        $payment = $project->payments()->create([
            'reference' => 'RH-PAY-TEST',
            'amount' => 1000,
            'currency' => 'LKR',
            'status' => Payment::STATUS_PENDING,
        ]);

        $this->actingAs($other)->get(route('homeowner.quotations.show', [$project, $quotation]))->assertForbidden();
        $this->actingAs($other)->get(route('homeowner.change-requests.show', [$project, $change]))->assertForbidden();
        $this->actingAs($other)->get(route('homeowner.projects.documents.download', [$project, $document]))->assertForbidden();
        $this->actingAs($other)->get(route('homeowner.projects.messages', $project))->assertForbidden();
        $this->actingAs($other)->get(route('homeowner.payments.show', [$project, $payment]))->assertForbidden();

        $this->actingAs($owner)->get(route('homeowner.quotations.show', [$project, $quotation]))->assertOk();
        $this->actingAs($owner)->post(route('homeowner.projects.change-requests.store', $project), [
            'title' => 'Move the island',
            'description' => 'Shift the island toward the window.',
            'category' => 'scope',
            'priority' => 'high',
        ])->assertRedirect();
        $this->assertSame(2, $project->changeRequests()->count());
    }

    public function test_api_requires_sanctum_and_hides_other_projects(): void
    {
        $owner = User::factory()->create(['role' => 'homeowner']);
        $other = User::factory()->create(['role' => 'homeowner']);
        Project::factory()->for($owner, 'homeowner')->count(12)->create();
        $foreign = Project::factory()->for($other, 'homeowner')->create();

        $this->getJson('/api/v1/projects')->assertUnauthorized();

        Sanctum::actingAs($owner);
        $this->getJson('/api/v1/projects')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('meta.per_page', 10)
            ->assertJsonPath('meta.total', 12);

        $this->postJson('/api/v1/projects', [])->assertStatus(422)->assertJsonPath('success', false);

        $this->getJson('/api/v1/projects/'.$foreign->id)->assertForbidden();

        Sanctum::actingAs($other);
        $this->getJson('/api/v1/projects/'.$foreign->id)
            ->assertOk()
            ->assertJsonPath('data.name', $foreign->name);
    }

    public function test_homeowner_can_upload_a_private_document(): void
    {
        Storage::fake('local');
        $homeowner = User::factory()->create(['role' => 'homeowner']);
        $project = Project::factory()->for($homeowner, 'homeowner')->create();

        $this->actingAs($homeowner)
            ->post(route('homeowner.projects.documents.store', $project), [
                'name' => 'Signed contract',
                'category' => 'contracts',
                'file' => UploadedFile::fake()->create('contract.pdf', 20, 'application/pdf'),
            ])
            ->assertRedirect();

        $this->assertSame(1, $project->documents()->count());
        Storage::disk('local')->assertExists($project->documents()->first()->path);
    }

    public function test_my_projects_catalogue_uses_top_navigation_and_filters(): void
    {
        $homeowner = User::factory()->create(['role' => 'homeowner', 'name' => 'Roshel Perera']);
        Project::factory()->for($homeowner, 'homeowner')->create([
            'name' => 'Modern Villa Renovation',
            'status' => Project::STATUS_COMPLETED,
            'city' => 'Colombo',
            'estimated_budget' => 8500000,
        ]);
        Project::factory()->for($homeowner, 'homeowner')->create([
            'name' => 'Outdoor Living Extension',
            'status' => Project::STATUS_IN_PROGRESS,
            'city' => 'Mount Lavinia',
            'renovation_type' => 'outdoor',
            'estimated_budget' => 3200000,
        ]);

        $this->actingAs($homeowner)
            ->get(route('homeowner.projects.index'))
            ->assertOk()
            ->assertSee('My Projects')
            ->assertSee('Modern Villa Renovation')
            ->assertSee('Outdoor Living Extension')
            ->assertSee('Total Project Value')
            ->assertSee('+ Create Project')
            ->assertSee('Homeowner');

        $this->actingAs($homeowner)
            ->get(route('homeowner.projects.index', ['status' => 'completed']))
            ->assertOk()
            ->assertSee('Modern Villa Renovation')
            ->assertDontSee('Outdoor Living Extension');

        $this->actingAs($homeowner)
            ->get(route('homeowner.projects.index', ['search' => 'Outdoor']))
            ->assertOk()
            ->assertSee('Outdoor Living Extension')
            ->assertDontSee('Modern Villa Renovation');
    }

    public function test_homeowner_home_explore_and_professional_profile_match_the_new_workspace(): void
    {
        $homeowner = User::factory()->create(['role' => 'homeowner', 'name' => 'Roshel Peter']);
        $designer = User::factory()->create(['role' => 'designer', 'name' => 'Amaya Senarath']);
        $profile = $designer->professionalProfile()->create([
            'professional_type' => 'designer',
            'title' => 'Interior Designer',
            'specialization' => 'Modern, Minimal, Contemporary',
            'tags' => ['Modern', 'Minimal', 'Contemporary'],
            'bio' => 'Interior designer with a focus on modern and functional living spaces.',
            'location' => 'Colombo',
            'avatar_path' => 'images/professionals/amaya-senarath.jpg',
            'cover_path' => 'images/renova/feature-collab.jpg',
            'years_experience' => 6,
            'completed_projects_count' => 50,
            'client_satisfaction' => 95,
            'rating' => 4.8,
            'review_count' => 42,
            'starting_price' => 5000,
            'listed' => true,
            'featured' => true,
            'verified' => true,
        ]);
        $profile->portfolioItems()->create([
            'title' => 'Villa living',
            'description' => 'Open living space.',
            'image' => 'images/renova/about-interior.jpg',
            'category' => 'Residential',
            'location' => 'Colombo',
            'budget_min' => 800000,
            'budget_max' => 1400000,
            'completion_year' => 2025,
        ]);
        $nethmi = User::factory()->create(['role' => 'designer', 'name' => 'Nethmi Wijesinghe']);
        $nethmi->professionalProfile()->create([
            'professional_type' => 'designer',
            'title' => 'Interior Designer',
            'specialization' => 'Modern, Minimal, Residential',
            'tags' => ['Modern', 'Minimal', 'Residential'],
            'bio' => 'Interior designer with a focus on modern and functional living spaces.',
            'location' => 'Colombo',
            'avatar_path' => 'images/professionals/nethmi-wijesinghe.jpg',
            'cover_path' => 'images/renova/feature-collab.jpg',
            'years_experience' => 7,
            'completed_projects_count' => 28,
            'rating' => 4.8,
            'review_count' => 28,
            'starting_price' => 200000,
            'listed' => true,
            'featured' => false,
            'verified' => true,
        ]);
        $case = $profile->caseStudies()->create([
            'slug' => 'modern-villa-renovation',
            'title' => 'Modern Villa Renovation',
            'category' => 'Residential',
            'location' => 'Colombo',
            'property_type' => 'Residential',
            'project_type' => 'Full Home Renovation',
            'size_sq_ft' => 3200,
            'budget' => 28500000,
            'completed_on' => '2025-03-01',
            'summary' => 'A complete interior and exterior renovation of a two-storey villa.',
            'overview' => 'The goal of this project was to transform an older villa.',
            'highlights' => ['Open-plan living'],
            'process' => [['title' => 'Concept & Mood Board', 'body' => 'Initial concepts', 'image' => 'images/renova/feature-green.jpg']],
            'hero_image' => 'images/renova/about-interior.jpg',
            'featured' => true,
            'sort_order' => 1,
        ]);
        $contractor = User::factory()->create(['role' => 'contractor', 'name' => 'Lanka Build Co']);
        $contractor->professionalProfile()->create([
            'professional_type' => 'contractor',
            'business_name' => 'Lanka Build Co',
            'title' => 'Renovation Contractor',
            'specialization' => 'Residential Renovation',
            'tags' => ['Residential Renovation', 'Kitchen Renovation'],
            'bio' => 'Residential renovation and kitchen fit-out team based in Colombo.',
            'location' => 'Colombo',
            'avatar_path' => 'images/renova/about-exterior.jpg',
            'cover_path' => 'images/renova/feature-progress.jpg',
            'years_experience' => 12,
            'completed_projects_count' => 48,
            'rating' => 4.8,
            'review_count' => 36,
            'starting_price' => 900000,
            'listed' => true,
            'featured' => true,
            'verified' => true,
        ]);

        $this->actingAs($homeowner)
            ->get(route('homeowner.home'))
            ->assertOk()
            ->assertSee('Bring your renovation')
            ->assertSee('Spaces')
            ->assertSee('Browse Professionals')
            ->assertSee('Tasks')
            ->assertSee('Messages')
            ->assertSee(route('homeowner.explore', [], false));

        $this->actingAs($homeowner)
            ->get(route('homeowner.explore'))
            ->assertOk()
            ->assertSee('Find Trusted Professionals')
            ->assertSee('Amaya Senarath')
            ->assertSee('View Portfolio')
            ->assertDontSee('View Profile');

        $this->actingAs($homeowner)
            ->get(route('homeowner.professionals.show', $designer))
            ->assertOk()
            ->assertSee('Amaya Senarath')
            ->assertSee('Project Portfolio');

        $this->actingAs($homeowner)
            ->get(route('homeowner.professionals.project', [$designer, $case->slug]))
            ->assertOk()
            ->assertSee('Modern Villa Renovation')
            ->assertSee('Design Process');

        \Livewire\Livewire::actingAs($homeowner)
            ->test(\App\Livewire\ExploreProfessionals::class)
            ->assertSee('Amaya Senarath')
            ->set('type', 'contractor')
            ->assertSee('Lanka Build Co')
            ->assertDontSee('Amaya Senarath');
    }

    public function test_contacting_a_professional_opens_one_conversation_and_blocks_idor(): void
    {
        $homeowner = User::factory()->create(['role' => 'homeowner', 'name' => 'Roshel Peter']);
        $other = User::factory()->create(['role' => 'homeowner']);
        $project = Project::factory()->for($homeowner, 'homeowner')->create(['name' => 'Modern Villa Renovation', 'city' => 'Colombo']);
        $designer = User::factory()->create(['role' => 'designer', 'name' => 'Amaya Senarath']);
        $designer->professionalProfile()->create([
            'professional_type' => 'designer',
            'title' => 'Interior Designer',
            'bio' => 'Interior designer with a focus on modern living spaces.',
            'listed' => true,
            'location' => 'Colombo',
        ]);
        $contractor = User::factory()->create(['role' => 'contractor', 'name' => 'Lanka Build Co']);
        $contractor->professionalProfile()->create([
            'professional_type' => 'contractor',
            'title' => 'Renovation Contractor',
            'bio' => 'Residential renovation contractor based in Colombo.',
            'listed' => true,
            'location' => 'Colombo',
        ]);

        $this->actingAs($homeowner)
            ->post(route('homeowner.professionals.contact', $designer))
            ->assertRedirect();

        $conversation = Conversation::query()->first();
        $this->assertNotNull($conversation);
        $this->assertSame($project->id, $conversation->project_id);
        $this->assertSame(1, Conversation::query()->count());

        $this->actingAs($homeowner)
            ->get(route('homeowner.messages.show', $conversation))
            ->assertOk()
            ->assertSee('Amaya Senarath')
            ->assertSee('Messages');

        $this->actingAs($homeowner)
            ->post(route('homeowner.professionals.contact', $designer))
            ->assertRedirect(route('homeowner.messages.show', $conversation));
        $this->assertSame(1, Conversation::query()->count());

        $this->actingAs($homeowner)
            ->post(route('homeowner.professionals.contact', $contractor))
            ->assertRedirect();
        $this->assertSame(2, Conversation::query()->count());

        $this->actingAs($other)
            ->get(route('homeowner.messages.show', $conversation))
            ->assertForbidden();

        \Livewire\Livewire::actingAs($homeowner)
            ->test(\App\Livewire\MessagesInbox::class, ['conversationId' => $conversation->id])
            ->set('body', 'Can we start with the kitchen?')
            ->call('send')
            ->assertSee('Can we start with the kitchen?');

        $this->assertDatabaseHas('conversation_messages', [
            'conversation_id' => $conversation->id,
            'sender_id' => $homeowner->id,
            'body' => 'Can we start with the kitchen?',
        ]);
    }
}
