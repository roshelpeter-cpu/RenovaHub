<x-designer-layout title="Mood Boards">
    <h1 class="font-serif text-4xl text-[#123D2B] sm:text-5xl">Mood Boards</h1>
    <p class="mt-2 text-sm text-[#66756C]">Create and submit the official design direction. A board becomes final only after the homeowner approves it.</p>
    <div class="mt-6 space-y-4">
        @forelse ($projects as $project)
            @php $board = $project->moodBoard; @endphp
            <article class="rounded-[1.4rem] border border-[#ece7dc] bg-white p-4 shadow-sm sm:p-5">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="font-serif text-2xl text-[#123D2B]">{{ $project->name }}</h2>
                        <p class="text-sm text-[#66756C]">{{ $board?->statusLabel() ?? 'Not started' }}</p>
                    </div>
                    <a href="{{ route('designer.projects.mood-board', $project) }}" class="rounded-full bg-[#123D2B] px-4 py-2 text-sm text-white">Open board</a>
                </div>
            </article>
        @empty
            <p class="text-sm text-[#66756C]">Mood boards appear after you accept a project.</p>
        @endforelse
    </div>
</x-designer-layout>
