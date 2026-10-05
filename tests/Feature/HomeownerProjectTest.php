<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeownerProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_homeowner_can_open_project_creation(): void
    {
        $homeowner = User::factory()->create(['role' => 'homeowner']);

        $this->actingAs($homeowner)
            ->get(route('homeowner.projects.create'))
            ->assertOk()
            ->assertSee('Create New Project');
    }

    public function test_designer_cannot_create_a_homeowner_project(): void
    {
        $designer = User::factory()->create(['role' => 'designer']);

        $this->actingAs($designer)
            ->get(route('homeowner.projects.create'))
            ->assertForbidden();
    }

    public function test_contractor_cannot_create_a_homeowner_project(): void
    {
        $contractor = User::factory()->create(['role' => 'contractor']);

        $this->actingAs($contractor)
            ->get(route('homeowner.projects.create'))
            ->assertForbidden();
    }

    public function test_homeowner_project_is_owned_by_the_authenticated_user(): void
    {
        $homeowner = User::factory()->create(['role' => 'homeowner']);
        $other = User::factory()->create(['role' => 'homeowner']);

        $this->actingAs($homeowner)
            ->post(route('homeowner.projects.store'), [
                'name' => 'Garden House',
                'description' => 'A calm renovation of the family home.',
                'renovation_type' => 'full_house',
                'property_type' => 'house',
                'user_id' => $other->id,
            ])
            ->assertRedirect();

        $project = Project::query()->first();

        $this->assertNotNull($project);
        $this->assertSame($homeowner->id, $project->user_id);
        $this->assertSame(Project::STATUS_PLANNING, $project->status);
        $this->assertNotSame($other->id, $project->user_id);
    }

    public function test_another_homeowner_cannot_open_the_project_location(): void
    {
        $owner = User::factory()->create(['role' => 'homeowner']);
        $intruder = User::factory()->create(['role' => 'homeowner']);
        $project = Project::factory()->for($owner, 'homeowner')->create();

        $this->actingAs($intruder)
            ->get(route('homeowner.projects.location', $project))
            ->assertForbidden();
    }

    public function test_invalid_project_details_are_rejected(): void
    {
        $homeowner = User::factory()->create(['role' => 'homeowner']);

        $this->actingAs($homeowner)
            ->from(route('homeowner.projects.create'))
            ->post(route('homeowner.projects.store'), [
                'name' => '',
                'description' => '',
                'renovation_type' => 'castle',
                'property_type' => 'house',
            ])
            ->assertRedirect(route('homeowner.projects.create'))
            ->assertSessionHasErrors(['name', 'description', 'renovation_type']);

        $this->assertSame(0, Project::query()->count());
    }

    public function test_location_coordinates_are_validated(): void
    {
        $homeowner = User::factory()->create(['role' => 'homeowner']);
        $project = Project::factory()->for($homeowner, 'homeowner')->create();

        $this->actingAs($homeowner)
            ->from(route('homeowner.projects.location', $project))
            ->put(route('homeowner.projects.location.update', $project), [
                'address' => '25 Flower Road',
                'city' => 'Colombo',
                'latitude' => 95,
                'longitude' => 200,
            ])
            ->assertRedirect(route('homeowner.projects.location', $project))
            ->assertSessionHasErrors(['latitude', 'longitude']);
    }

    public function test_location_can_be_saved_without_coordinates(): void
    {
        $homeowner = User::factory()->create(['role' => 'homeowner']);
        $project = Project::factory()->for($homeowner, 'homeowner')->create();

        $this->actingAs($homeowner)
            ->put(route('homeowner.projects.location.update', $project), [
                'address' => '25 Flower Road',
                'city' => 'Colombo',
                'province' => 'Western',
                'postal_code' => '00700',
                'latitude' => '',
                'longitude' => '',
            ])
            ->assertRedirect(route('homeowner.projects.budget', $project));

        $project->refresh();

        $this->assertSame('25 Flower Road', $project->address);
        $this->assertSame('Colombo', $project->city);
        $this->assertNull($project->latitude);
        $this->assertNull($project->longitude);
    }

    public function test_landing_page_remains_available(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Renovate')
            ->assertSee('Contact')
            ->assertSee('Create Your Project')
            ->assertSee('Manage the Renovation')
            ->assertDontSee('Play Video');
    }

    public function test_homeowner_can_update_and_delete_only_their_project(): void
    {
        $owner = User::factory()->create(['role' => 'homeowner']);
        $other = User::factory()->create(['role' => 'homeowner']);
        $project = Project::factory()->for($owner, 'homeowner')->create();

        $this->actingAs($owner)
            ->put(route('homeowner.projects.update', $project), [
                'name' => 'Updated House',
                'description' => 'A revised description for the renovation.',
                'renovation_type' => 'kitchen',
                'property_type' => 'apartment',
                'status' => 'planning',
                'progress' => 20,
            ])
            ->assertRedirect(route('homeowner.projects.show', $project));

        $this->assertSame('Updated House', $project->fresh()->name);

        $this->actingAs($other)
            ->delete(route('homeowner.projects.destroy', $project))
            ->assertForbidden();

        $this->actingAs($owner)
            ->delete(route('homeowner.projects.destroy', $project))
            ->assertRedirect(route('homeowner.projects.index'));

        $this->assertModelMissing($project);
    }

    public function test_team_selection_accepts_only_matching_roles(): void
    {
        $homeowner = User::factory()->create(['role' => 'homeowner']);
        $designer = User::factory()->create(['role' => 'designer']);
        $contractor = User::factory()->create(['role' => 'contractor']);
        $project = Project::factory()->for($homeowner, 'homeowner')->create();

        $this->actingAs($homeowner)
            ->put(route('homeowner.projects.team.update', $project), [
                'designer_id' => $contractor->id,
                'contractor_id' => $designer->id,
                'complete' => 1,
            ])
            ->assertSessionHasErrors(['designer_id', 'contractor_id']);

        $this->actingAs($homeowner)
            ->put(route('homeowner.projects.team.update', $project), [
                'designer_id' => $designer->id,
                'contractor_id' => $contractor->id,
                'complete' => 1,
            ])
            ->assertRedirect(route('homeowner.projects.show', $project));

        $project->refresh();
        $this->assertNull($project->designer_id);
        $this->assertNull($project->contractor_id);
        $this->assertNotNull($project->invitations()->where('user_id', $designer->id)->where('role', 'designer')->where('status', 'pending')->first());
        $this->assertNotNull($project->invitations()->where('user_id', $contractor->id)->where('role', 'contractor')->where('status', 'pending')->first());
    }

    public function test_homeowner_dashboard_lists_their_projects(): void
    {
        $homeowner = User::factory()->create(['role' => 'homeowner']);
        Project::factory()->for($homeowner, 'homeowner')->create(['name' => 'Garden House']);

        $this->actingAs($homeowner)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Garden House');
    }
}
