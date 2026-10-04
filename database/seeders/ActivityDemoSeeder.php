<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ActivityDemoSeeder extends Seeder
{
    /**
     * Home Recent Activity is read from this table. Copy and timestamps
     * belong here so Blade never hardcodes the feed.
     */
    public function run(): void
    {
        $homeowner = User::query()->where('email', 'homeowner@test.com')->firstOrFail();
        $activity = app(ActivityLogService::class);

        foreach ($homeowner->projects as $project) {
            $project->activity()->delete();
            $start = Carbon::parse($project->expected_start_date);

            if ($project->name === 'Lakeview Villa Renovation') {
                $this->villaFeed($activity, $project, $homeowner);

                continue;
            }

            $activity->record($project, $homeowner, 'project.created', 'Project created.', null, $start->copy()->subDays(10));
            $activity->record($project, $project->designer, 'invitation.accepted', 'The designer accepted the invitation.', null, $start->copy()->subDays(7));
            $activity->record($project, $project->contractor, 'invitation.accepted', 'The contractor accepted the invitation.', null, $start->copy()->subDays(6));
        }
    }

    private function villaFeed(ActivityLogService $activity, Project $project, User $homeowner): void
    {
        $today = now()->startOfDay();
        $yesterday = now()->subDay()->startOfDay();

        $activity->record(
            $project,
            $project->contractor,
            'quotation.submitted',
            'Contractor submitted a new quotation',
            ['detail' => 'Kitchen renovation - LKR 485,000'],
            $today->copy()->setTime(10, 24),
        );
        $activity->record(
            $project,
            $project->designer,
            'moodboard.updated',
            'Designer uploaded a revised kitchen concept',
            null,
            $today->copy()->setTime(9, 15),
        );
        $activity->record(
            $project,
            $homeowner,
            'design.approved',
            'You approved the living room design',
            null,
            $today->copy()->setTime(8, 40),
        );
        $activity->record(
            $project,
            $project->contractor,
            'quotation.submitted',
            'New material quotation received',
            ['detail' => 'Outdoor seating area - LKR 320,000'],
            $yesterday->copy()->setTime(16, 30),
        );
        $activity->record(
            $project,
            $project->contractor,
            'task.updated',
            'Construction task marked as completed',
            ['detail' => 'Electrical installation'],
            $yesterday->copy()->setTime(14, 10),
        );
    }
}
