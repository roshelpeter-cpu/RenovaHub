<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ConversationService
{
    /**
     * Find an existing conversation first so contacting the same professional
     * does not create duplicate threads for the same homeowner and project.
     */
    public function findOrCreate(User $homeowner, User $professional, ?Project $project = null): Conversation
    {
        abort_unless($homeowner->isHomeowner(), 403);
        abort_unless($professional->isDesigner() || $professional->isContractor() || $professional->email === 'support@renovahub.test', 404);

        $existing = Conversation::query()
            ->where('kind', $professional->email === 'support@renovahub.test' ? 'support' : 'professional')
            ->where('project_id', $project?->id)
            ->whereHas('participants', fn ($query) => $query->where('users.id', $homeowner->id))
            ->whereHas('participants', fn ($query) => $query->where('users.id', $professional->id))
            ->first();

        if ($existing) {
            return $existing;
        }

        return DB::transaction(function () use ($homeowner, $professional, $project) {
            $conversation = Conversation::query()->create([
                'project_id' => $project?->id,
                'kind' => $professional->email === 'support@renovahub.test' ? 'support' : 'professional',
                'last_message_at' => now(),
            ]);
            $conversation->participants()->attach([$homeowner->id, $professional->id]);

            return $conversation;
        });
    }

    /**
     * @return Collection<int, Conversation>
     */
    public function inbox(User $homeowner, string $tab, string $search): Collection
    {
        $query = Conversation::query()
            ->whereHas('participants', fn ($inner) => $inner->where('users.id', $homeowner->id))
            ->with(['participants.professionalProfile', 'project', 'participantRows', 'messages.attachments'])
            ->orderByDesc('last_message_at')
            ->orderByDesc('id');

        if ($tab === 'designers') {
            $query->whereHas('participants', fn ($inner) => $inner->where('users.role', 'designer')->where('users.id', '!=', $homeowner->id));
        } elseif ($tab === 'contractors') {
            $query->whereHas('participants', fn ($inner) => $inner->where('users.role', 'contractor')->where('users.id', '!=', $homeowner->id));
        } elseif ($tab === 'support') {
            $query->where('kind', 'support');
        }

        $conversations = $query->get();

        if ($search !== '') {
            $term = Str::lower($search);
            $conversations = $conversations->filter(function (Conversation $conversation) use ($homeowner, $term) {
                $other = $conversation->counterpart($homeowner);

                return $other && str_contains(Str::lower($other->name), $term);
            })->values();
        }

        return $conversations;
    }

    /**
     * @param  list<UploadedFile>  $files
     */
    public function send(User $homeowner, Conversation $conversation, string $body, array $files = []): ConversationMessage
    {
        abort_unless($homeowner->can('update', $conversation), 403);
        abort_if($body === '' && $files === [], 422);

        $message = $conversation->messages()->create([
            'sender_id' => $homeowner->id,
            'body' => $body !== '' ? $body : null,
            'read_at' => now(),
        ]);

        foreach ($files as $file) {
            $message->attachments()->create([
                'path' => $file->store('conversations/'.$conversation->id, 'public'),
                'original_name' => $file->getClientOriginalName(),
            ]);
        }

        $preview = $body !== '' ? Str::limit($body, 80) : 'Photo';
        $conversation->forceFill([
            'last_preview' => $preview,
            'last_message_at' => now(),
        ])->save();

        $conversation->participantRows()->where('user_id', $homeowner->id)->update(['last_read_at' => now()]);

        return $message;
    }

    public function markRead(User $homeowner, Conversation $conversation): void
    {
        $conversation->participantRows()->where('user_id', $homeowner->id)->update(['last_read_at' => now()]);
    }

    public function projectFor(User $homeowner): ?Project
    {
        return $homeowner->projects()->where('name', 'Modern Villa Renovation')->first()
            ?? $homeowner->projects()->latest()->first();
    }
}
