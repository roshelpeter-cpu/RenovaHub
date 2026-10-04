<?php

namespace App\Livewire;

use App\Models\Conversation;
use App\Services\ConversationService;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithFileUploads;

class MessagesInbox extends Component
{
    use WithFileUploads;

    public ?int $selectedId = null;

    public string $tab = 'all';

    public string $search = '';

    public string $body = '';

    /**
     * @var array<int, mixed>
     */
    public array $photos = [];

    public function mount(?int $conversationId = null): void
    {
        Gate::authorize('viewAny', \App\Models\Project::class);

        if ($conversationId !== null) {
            $this->open($conversationId);
        }
    }

    public function open(int $id): void
    {
        $conversation = Conversation::query()->findOrFail($id);
        $this->authorize('view', $conversation);
        $this->selectedId = $id;
        app(ConversationService::class)->markRead(auth()->user(), $conversation);
    }

    public function send(ConversationService $conversations): void
    {
        $this->validate([
            'body' => ['nullable', 'string', 'max:4000'],
            'photos' => ['nullable', 'array', 'max:6'],
            'photos.*' => ['image', 'max:5120'],
        ]);

        abort_if($this->selectedId === null, 422);

        $conversation = Conversation::query()->findOrFail($this->selectedId);
        $body = trim($this->body);

        abort_if($body === '' && $this->photos === [], 422);

        $conversations->send(auth()->user(), $conversation, $body, $this->photos);
        $this->reset('body', 'photos');
        $this->selectedId = $conversation->id;
    }

    public function render(ConversationService $conversations)
    {
        $user = auth()->user();
        $inbox = $conversations->inbox($user, $this->tab, trim($this->search));
        $selected = $inbox->firstWhere('id', $this->selectedId);

        // URL contact redirects pass an id that may not yet be in the filtered tab.
        if ($this->selectedId && $selected === null) {
            $selected = Conversation::query()
                ->with(['participants.professionalProfile', 'project', 'participantRows', 'messages.attachments'])
                ->find($this->selectedId);

            if ($selected && ! $user->can('view', $selected)) {
                abort(403);
            }
        }

        return view('livewire.messages-inbox', [
            'conversations' => $inbox,
            'selected' => $selected,
        ]);
    }
}
