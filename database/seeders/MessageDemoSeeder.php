<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class MessageDemoSeeder extends Seeder
{
    public function run(): void
    {
        $homeowner = User::query()->where('email', 'homeowner@test.com')->firstOrFail();

        foreach ($homeowner->projects as $project) {
            $project->messages()->delete();
            $project->messages()->create([
                'sender_id' => $homeowner->id,
                'body' => 'Can we keep the garden doors as the main light source?',
                'read_at' => now()->subDays(3),
                'created_at' => now()->subDays(4),
            ]);
        }

        $family = $homeowner->projects()->where('name', 'Family Home Extension')->firstOrFail();
        $outdoor = $homeowner->projects()->where('name', 'Apartment Interior Makeover')->firstOrFail();

        foreach ([
            [$family, $family->designer_id, 'I have updated the kitchen island tone. Please have a look.'],
            [$family, $family->contractor_id, 'The cabinet carcasses arrive on Thursday.'],
            [$outdoor, $outdoor->designer_id, 'The screen can step down toward the lane if you prefer.'],
            [$outdoor, $outdoor->contractor_id, 'Drainage trenching starts next week, weather permitting.'],
        ] as [$project, $sender, $body]) {
            $project->messages()->create([
                'sender_id' => $sender,
                'body' => $body,
                'read_at' => null,
            ]);
        }
    }
}
