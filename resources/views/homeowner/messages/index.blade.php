<x-homeowner-layout title="Messages">
    <h1 class="font-serif text-3xl text-forest sm:text-4xl">Messages</h1>
    <p class="mt-2 text-sm text-mist">Each conversation belongs to one project. There is no public chat.</p>
    <div class="mt-6 space-y-3">
        @forelse ($projects as $project)
            <a href="{{ route('homeowner.projects.messages', $project) }}" class="flex items-center justify-between rounded-3xl border border-[#ece7dc] bg-white p-5 shadow-sm">
                <div>
                    <h2 class="font-serif text-xl text-forest">{{ $project->name }}</h2>
                    <p class="mt-1 text-sm text-mist">{{ $project->messages->first()?->body ?? 'No messages yet' }}</p>
                </div>
                @if ($project->unread_messages_count)
                    <span class="rounded-full bg-[#DCE7D8] px-2 py-1 text-xs text-forest">{{ $project->unread_messages_count }}</span>
                @endif
            </a>
        @empty
            @include('homeowner.partials.empty', ['title' => 'No messages', 'body' => 'Project conversations appear after you create a project.'])
        @endforelse
    </div>
</x-homeowner-layout>
