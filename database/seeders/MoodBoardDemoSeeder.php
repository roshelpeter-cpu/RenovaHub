<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class MoodBoardDemoSeeder extends Seeder
{
    public function run(): void
    {
        $homeowner = User::query()->where('email', 'homeowner@test.com')->firstOrFail();

        foreach ($homeowner->projects as $project) {
            $project->moodBoard()?->delete();
            $board = $project->moodBoard()->create([
                'created_by' => $project->designer_id,
                'title' => $project->name.' direction',
                'summary' => 'Warm timber, soft sage walls and daylight toward the garden.',
                'version' => $project->name === 'Modern Villa Renovation' ? 2 : ($project->status === 'completed' ? 3 : 1),
                'approved_at' => $project->name === 'Modern Villa Renovation' ? null : ($project->status === 'completed' ? $project->actual_completion_date : now()->subWeek()),
            ]);

            foreach ([
                ['image', 'Living inspiration', 'images/renova/about-interior.jpg', null, 'A calm seating arrangement.'],
                ['image', 'Material sample', 'images/renova/feature-green.jpg', null, 'Planting and stone.'],
                ['colour', 'Sage wall', null, '#DCE7D8', 'Main wall colour.'],
                ['colour', 'Timber', null, '#8C6239', 'Joinery tone.'],
                ['note', 'Lighting', null, null, 'Warm lamps, no cool downlights in the living room.'],
            ] as [$kind, $title, $image, $colour, $body]) {
                $board->items()->create([
                    'kind' => $kind,
                    'title' => $title,
                    'image' => $image,
                    'colour' => $colour,
                    'body' => $body,
                ]);
            }

            if ($project->name === 'Modern Villa Renovation') {
                $board->feedback()->create([
                    'user_id' => $homeowner->id,
                    'title' => 'Living Room - Revision 02',
                    'comment' => 'Please keep the warmer timber and the open seating arrangement.',
                ]);
            }
        }
    }
}
