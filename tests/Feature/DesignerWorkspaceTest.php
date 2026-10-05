<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\DesignChangeRequest;
use App\Models\DesignerEarning;
use App\Models\MoodBoard;
use App\Models\Project;
use App\Models\ProjectInvitation;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DesignerWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_designer_workspace_follows_offer_and_approval_rules(): void
    {
        $homeowner = User::factory()->create(['role' => 'homeowner']);
        $designer = User::factory()->create(['role' => 'designer', 'email' => 'designer@test.com']);
        $other = User::factory()->create(['role' => 'designer']);

        $pending = Project::factory()->create([
            'user_id' => $homeowner->id,
            'name' => 'Modern House Renovation',
            'designer_id' => null,
            'status' => Project::STATUS_AWAITING_TEAM,
        ]);
        $invitation = $pending->invitations()->create([
            'user_id' => $designer->id,
            'role' => 'designer',
            'status' => ProjectInvitation::STATUS_PENDING,
        ]);

        $this->actingAs($homeowner)->get(route('designer.dashboard'))->assertForbidden();
        $this->actingAs($designer)->get(route('designer.dashboard'))->assertOk()->assertSee('Pending Invitations');
        $this->actingAs($designer)->get(route('designer.invitations.index'))->assertSee('Modern House Renovation');

        $this->actingAs($designer)->post(route('designer.invitations.accept', $invitation))
            ->assertRedirect(route('designer.projects.show', $pending));

        $pending->refresh();
        $this->assertSame($designer->id, $pending->designer_id);
        $this->actingAs($designer)->get(route('designer.projects.index'))->assertSee('Modern House Renovation');
        $this->actingAs($other)->get(route('designer.projects.show', $pending))->assertNotFound();

        $this->actingAs($designer)->post(route('designer.projects.mood-board.items.store', $pending), [
            'kind' => 'note',
            'title' => 'Kitchen direction',
            'body' => 'Warm oak and linen.',
        ])->assertRedirect();

        $this->actingAs($designer)->post(route('designer.projects.mood-board.submit', $pending))->assertRedirect();
        $board = $pending->moodBoard()->first();
        $this->assertSame(MoodBoard::STATUS_AWAITING, $board->status);
        $this->assertNull($board->approved_at);

        $this->actingAs($homeowner)->post(route('homeowner.projects.mood-board.request-changes', $pending), [
            'revision_note' => 'Please warm the kitchen cabinets.',
        ])->assertRedirect();
        $this->assertSame(MoodBoard::STATUS_REVISION, $board->fresh()->status);

        $this->actingAs($designer)->post(route('designer.projects.mood-board.submit', $pending))->assertRedirect();
        $this->actingAs($homeowner)->post(route('homeowner.projects.mood-board.approve', $pending))->assertRedirect();
        $board->refresh();
        $this->assertTrue($board->isFinal());

        $change = $pending->designChangeRequests()->create([
            'requested_by' => $homeowner->id,
            'title' => 'Darker walnut cabinets',
            'description' => 'Change kitchen cabinets to darker walnut.',
            'status' => DesignChangeRequest::STATUS_PENDING,
        ]);

        $this->actingAs($designer)->post(route('designer.revisions.reject', $change), [
            'rejection_reason' => 'This change conflicts with the approved material palette.',
        ])->assertRedirect();
        $this->assertSame(DesignChangeRequest::STATUS_REJECTED, $change->fresh()->status);
        $this->assertNotNull($change->fresh()->rejection_reason);

        $this->assertFalse($designer->can('create', [Quotation::class, $pending]));

        $conversation = Conversation::query()->create(['project_id' => $pending->id, 'kind' => 'professional']);
        $conversation->participants()->attach([$designer->id, $homeowner->id]);
        $theirs = $conversation->messages()->create(['sender_id' => $homeowner->id, 'body' => 'Please review the kitchen.']);
        $mine = $conversation->messages()->create(['sender_id' => $designer->id, 'body' => 'I will revise the cabinet tone.']);

        $this->assertFalse($designer->can('delete', $theirs));
        $this->assertTrue($designer->can('delete', $mine));

        $gross = 500000;
        $this->assertSame(10000.0, DesignerEarning::feeFor($gross));
        $this->assertSame(490000.0, DesignerEarning::netFor($gross));
    }

    public function test_a_rejected_offer_does_not_open_the_project(): void
    {
        $homeowner = User::factory()->create();
        $designer = User::factory()->create(['role' => 'designer']);
        $project = Project::factory()->create([
            'user_id' => $homeowner->id,
            'designer_id' => $designer->id,
            'status' => Project::STATUS_AWAITING_TEAM,
        ]);
        $invitation = $project->invitations()->create([
            'user_id' => $designer->id,
            'role' => 'designer',
            'status' => ProjectInvitation::STATUS_PENDING,
        ]);

        $this->actingAs($designer)->post(route('designer.invitations.reject', $invitation))->assertRedirect();
        $project->refresh();
        $this->assertNull($project->designer_id);
        $this->actingAs($designer)->get(route('designer.projects.show', $project))->assertNotFound();
    }
}
