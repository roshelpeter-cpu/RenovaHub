<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    protected $fillable = ['project_id', 'kind', 'last_preview', 'last_message_at'];

    protected function casts(): array
    {
        return ['last_message_at' => 'datetime'];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conversation_participants')->withPivot('last_read_at')->withTimestamps();
    }

    public function participantRows(): HasMany
    {
        return $this->hasMany(ConversationParticipant::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ConversationMessage::class)->orderBy('created_at');
    }

    public function counterpart(User $viewer): ?User
    {
        $this->loadMissing('participants.professionalProfile');

        return $this->participants->first(fn (User $user) => $user->id !== $viewer->id);
    }

    public function unreadFor(User $viewer): int
    {
        $readAt = $this->participantRows->firstWhere('user_id', $viewer->id)?->last_read_at;

        return $this->messages
            ->where('sender_id', '!=', $viewer->id)
            ->filter(fn (ConversationMessage $message) => $readAt === null || $message->created_at->gt($readAt))
            ->count();
    }

    public function isSupport(): bool
    {
        return $this->kind === 'support';
    }
}
