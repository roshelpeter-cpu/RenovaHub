<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DocumentDemoSeeder extends Seeder
{
    public function run(): void
    {
        $homeowner = User::query()->where('email', 'homeowner@test.com')->firstOrFail();
        $source = public_path('images/renova/feature-docs.jpg');
        $bytes = is_file($source) ? file_get_contents($source) : 'demo';

        $names = [
            ['Contract', 'contracts'],
            ['Floor plan', 'floor_plans'],
            ['Concept sheet', 'design'],
            ['Material schedule', 'materials'],
            ['Invoice', 'invoices'],
            ['Site photo record', 'construction'],
        ];

        foreach ($homeowner->projects as $project) {
            $project->documents()->delete();

            foreach ($names as [$name, $category]) {
                $path = 'projects/'.$project->id.'/documents/'.str($name)->slug().'.jpg';
                Storage::disk('local')->put($path, $bytes);
                $project->documents()->create([
                    'uploaded_by' => $project->designer_id,
                    'name' => $name,
                    'original_name' => str($name)->slug().'.jpg',
                    'category' => $category,
                    'disk' => 'local',
                    'path' => $path,
                    'mime' => 'image/jpeg',
                    'size' => strlen($bytes),
                ]);
            }
        }
    }
}
