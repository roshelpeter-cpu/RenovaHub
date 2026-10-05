<x-designer-layout title="Revisions">
    <h1 class="font-serif text-4xl text-[#123D2B] sm:text-5xl">Revisions</h1>
    <p class="mt-2 text-sm text-[#66756C]">Homeowner design change requests. Accept the work, or reject it with a reason.</p>
    <div class="mt-6 space-y-4">
        @forelse ($revisions as $revision)
            @include('designer.revisions.card', ['revision' => $revision])
        @empty
            <p class="text-sm text-[#66756C]">No design change requests.</p>
        @endforelse
    </div>
</x-designer-layout>
