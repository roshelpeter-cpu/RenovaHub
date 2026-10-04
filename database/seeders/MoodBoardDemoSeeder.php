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
                'version' => $project->name === 'Lakeview Villa Renovation' ? 2 : ($project->status === 'completed' ? 3 : 1),
                'approved_at' => $project->name === 'Lakeview Villa Renovation' ? null : ($project->status === 'completed' ? $project->actual_completion_date : now()->subWeek()),
            ]);

            $palette = $project->status === 'completed'
                ? [
                    ['colour', 'Warm White', null, '#F8F6F1'],
                    ['colour', 'Olive Green', null, '#8A7A5B'],
                    ['colour', 'Walnut Brown', null, '#3E3A36'],
                    ['colour', 'Beige', null, '#D7C9B1'],
                    ['colour', 'Charcoal', null, '#3E3E3E'],
                    ['material', 'Oak Wood', null, '#C4A574'],
                    ['material', 'Travertine Stone', null, '#E6D7C3'],
                    ['material', 'Matte Black', null, '#2C2C2C'],
                    ['material', 'Natural Stone', null, '#D9D2C5'],
                    ['material', 'Brushed Brass', null, '#C6A15B'],
                ]
                : [
                    ['colour', 'Warm Beige', null, '#F3EFE7'],
                    ['colour', 'Sage Green', null, '#A7B89F'],
                    ['colour', 'Terracotta', null, '#C97B5B'],
                    ['colour', 'Warm Grey', null, '#C9C3B6'],
                    ['colour', 'Black', null, '#212121'],
                    ['material', 'Light Oak', null, '#D7B48A'],
                    ['material', 'Concrete Finish', null, '#D5D2CC'],
                    ['material', 'Rattan', null, '#C6A36A'],
                    ['material', 'Matte White', null, '#F7F4EE'],
                    ['material', 'Brushed Brass', null, '#C6A15B'],
                ];

            foreach ([
                ['inspiration', 'Living room', 'images/renova/about-interior.jpg'],
                ['inspiration', 'Exterior', 'images/renova/about-exterior.jpg'],
                ['inspiration', 'Daylight', 'images/renova/hero.jpg'],
                ['inspiration', 'Garden', 'images/renova/feature-green.jpg'],
                ['inspiration', 'Detail', 'images/renova/feature-collab.jpg'],
                ['furniture', 'Sofa', 'images/renova/about-interior.jpg'],
                ['furniture', 'Dining Table', 'images/renova/feature-plans.jpg'],
                ['furniture', 'Pendant Light', 'images/renova/feature-notes.jpg'],
            ] as [$kind, $title, $image]) {
                $board->items()->create([
                    'kind' => $kind,
                    'title' => $title,
                    'image' => $image,
                    'body' => $title.' reference for '.$project->name.'.',
                ]);
            }

            foreach ($palette as [$kind, $title, $image, $colour]) {
                $board->items()->create([
                    'kind' => $kind,
                    'title' => $title,
                    'image' => $image,
                    'colour' => $colour,
                ]);
            }
        }
    }
}
