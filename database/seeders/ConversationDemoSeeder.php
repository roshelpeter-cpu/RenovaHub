<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\ConversationAttachment;
use App\Models\ConversationMessage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class ConversationDemoSeeder extends Seeder
{
    /**
     * Seed the WhatsApp-style inbox from real users so Contact Designer
     * reuses the Amaya thread instead of inventing a second copy.
     */
    public function run(): void
    {
        $homeowner = User::query()->where('email', 'homeowner@test.com')->firstOrFail();
        $villa = $homeowner->projects()->where('name', 'Lakeview Villa Renovation')->first()
            ?? $homeowner->projects()->where('name', 'Modern Villa Renovation')->first();

        $support = User::query()->updateOrCreate(
            ['email' => 'support@renovahub.test'],
            [
                'name' => 'RenovaHub Support',
                'password' => Hash::make('support-demo-only'),
                'role' => 'designer',
                'email_verified_at' => now(),
            ],
        );

        $threads = [
            [
                'email' => 'amaya.senarath@renovahub.test',
                'kind' => 'professional',
                'unread' => 2,
                'messages' => [
                    ['them', 'Hi Roshel! Thanks for reaching out. How can I help with your renovation project?', 'today 10:20'],
                    ['me', "Hi Amaya, I really liked your Modern Villa Renovation project.\nI'd like to discuss my home renovation requirements.", 'today 10:22'],
                    ['them', "Sure! I can prepare a few concepts based on your requirements.\nCould you share some details about your space and budget?", 'today 10:24'],
                    ['me', "Yes, of course. Here are some photos of the current space.\nWe are planning to renovate the living room and kitchen.\nOur budget is around LKR 3 - 4 million.", 'today 10:26', [
                        'images/renova/about-interior.jpg',
                        'images/renova/feature-collab.jpg',
                        'images/renova/auth-register.jpg',
                    ]],
                    ['them', "Great! These look amazing. I'll prepare a few design concepts and a rough quotation for you. I'll share them by tomorrow. Let me know if you have any specific style preferences (modern, minimal, Scandinavian, etc.).", 'today 10:28'],
                ],
            ],
            [
                'email' => 'dinesh.jayawardena@renovahub.test',
                'kind' => 'professional',
                'unread' => 1,
                'messages' => [
                    ['them', 'The quotation has been updated.', 'yesterday 16:10'],
                ],
            ],
            [
                'email' => 'sithumi.fernando@renovahub.test',
                'kind' => 'professional',
                'unread' => 0,
                'messages' => [
                    ['them', 'Let me know if you need any changes.', '2026-09-28 11:00'],
                ],
            ],
            [
                'email' => 'kavinda.perera@renovahub.test',
                'kind' => 'professional',
                'unread' => 0,
                'messages' => [
                    ['them', 'Can you share the site measurements?', '2026-09-26 09:15'],
                ],
            ],
            [
                'email' => 'support@renovahub.test',
                'kind' => 'support',
                'unread' => 0,
                'messages' => [
                    ['them', 'Your project has been updated.', '2026-09-25 14:00'],
                ],
            ],
        ];

        foreach ($threads as $thread) {
            $professional = $thread['email'] === 'support@renovahub.test'
                ? $support
                : User::query()->where('email', $thread['email'])->firstOrFail();

            $projectId = $thread['kind'] === 'support' ? null : $villa?->id;

            $conversation = Conversation::query()
                ->where('kind', $thread['kind'])
                ->where('project_id', $projectId)
                ->whereHas('participants', fn ($query) => $query->where('users.id', $homeowner->id))
                ->whereHas('participants', fn ($query) => $query->where('users.id', $professional->id))
                ->first();

            if ($conversation === null) {
                $conversation = Conversation::query()->create([
                    'project_id' => $projectId,
                    'kind' => $thread['kind'],
                ]);
                $conversation->participants()->attach([$homeowner->id, $professional->id]);
            }

            $conversation->messages()->delete();

            $lastAt = now();
            foreach ($thread['messages'] as $row) {
                [$who, $body, $when] = $row;
                $at = $this->stamp($when);
                $message = ConversationMessage::query()->create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => $who === 'me' ? $homeowner->id : $professional->id,
                    'body' => $body,
                    'created_at' => $at,
                    'updated_at' => $at,
                ]);
                foreach ($row[3] ?? [] as $path) {
                    ConversationAttachment::query()->create([
                        'conversation_message_id' => $message->id,
                        'path' => $path,
                        'original_name' => basename($path),
                    ]);
                }
                $lastAt = $at;
            }

            $conversation->forceFill([
                'last_preview' => $thread['messages'][array_key_last($thread['messages'])][1],
                'last_message_at' => $lastAt,
            ])->save();

            $readAt = $thread['unread'] > 0 ? $lastAt->copy()->subHours(6) : $lastAt;
            $conversation->participantRows()->where('user_id', $homeowner->id)->update(['last_read_at' => $readAt]);
        }
    }

    private function stamp(string $when): Carbon
    {
        if (str_starts_with($when, 'today ')) {
            return now()->setTimeFromTimeString(substr($when, 6));
        }

        if (str_starts_with($when, 'yesterday ')) {
            return now()->subDay()->setTimeFromTimeString(substr($when, 10));
        }

        return Carbon::parse($when);
    }
}
