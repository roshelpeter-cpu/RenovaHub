<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectFeedbackDemoSeeder extends Seeder
{
    /**
     * Completed projects keep a separate designer rating and contractor rating.
     */
    public function run(): void
    {
        $homeowner = User::query()->where('email', 'homeowner@test.com')->firstOrFail();

        foreach ($homeowner->projects()->where('status', 'completed')->get() as $project) {
            $project->feedback()->delete();

            if ($project->designer_id) {
                $project->feedback()->create([
                    'homeowner_id' => $homeowner->id,
                    'professional_id' => $project->designer_id,
                    'role' => 'designer',
                    'rating' => 5,
                    'title' => 'Thoughtful design direction',
                    'comment' => 'The designer kept the house calm and practical, and the final mood board matched how we live.',
                ]);
            }

            if ($project->contractor_id) {
                $project->feedback()->create([
                    'homeowner_id' => $homeowner->id,
                    'professional_id' => $project->contractor_id,
                    'role' => 'contractor',
                    'rating' => 5,
                    'title' => 'Careful construction',
                    'comment' => 'The contractor kept the programme clear and handed the house over in the condition we agreed.',
                ]);
            }
        }
    }
}
