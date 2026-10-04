<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class NotificationDemoSeeder extends Seeder
{
    public function run(): void
    {
        $homeowner = User::query()->where('email', 'homeowner@test.com')->firstOrFail();
        $homeowner->notifications()->delete();
        $notifier = app(NotificationService::class);

        $items = [
            ['Sarah Fernando accepted your invitation.', 'The family home team is confirmed.', 'projects', Carbon::parse('2026-03-02')],
            ['New quotation received', 'Q-241 is waiting for your decision.', 'quotations', now()->subDays(2)],
            ['Payment completed', 'A stage payment was recorded on the family home.', 'payments', now()->subDays(6)],
            ['Mood board updated', 'Sarah uploaded a revised kitchen concept.', 'design', now()->subDay()],
            ['New message', 'ABC Renovations sent a site update.', 'messages', now()->subHours(5)],
        ];

        foreach ($items as [$title, $body, $category, $at]) {
            $notifier->notify($homeowner, $title, $body, $category, route('homeowner.notifications.index'), $at);
        }
    }
}
