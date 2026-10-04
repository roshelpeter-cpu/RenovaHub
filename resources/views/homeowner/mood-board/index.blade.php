<x-homeowner-layout title="Mood Board">
    <h1 class="font-serif text-3xl text-forest sm:text-4xl">Mood Board</h1>
    <p class="mt-2 text-sm text-mist">Design direction for each renovation. Feedback here is separate from construction change requests.</p>
    <div class="mt-6 grid gap-4 md:grid-cols-2">
        @forelse ($projects as $project)
            <a href="{{ route('homeowner.projects.mood-board', $project) }}" class="rounded-3xl border border-[#ece7dc] bg-white p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl">
                <h2 class="font-serif text-2xl text-forest">{{ $project->name }}</h2>
                <p class="mt-2 text-sm text-mist">{{ $project->moodBoard ? 'Version '.$project->moodBoard->version.' · '.($project->moodBoard->approved_at ? 'Approved' : 'In review') : 'No mood board yet' }}</p>
            </a>
        @empty
            @include('homeowner.partials.empty', ['title' => 'No mood board yet', 'body' => 'A mood board appears after a project is created.'])
        @endforelse
    </div>
</x-homeowner-layout>
