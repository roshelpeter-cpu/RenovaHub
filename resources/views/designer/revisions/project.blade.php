<x-designer-layout title="Revisions">
    @include('designer.partials.project-header')
    <div class="mt-6 space-y-4">
        @forelse ($revisions as $revision)
            @include('designer.revisions.card', ['revision' => $revision])
        @empty
            <p class="text-sm text-[#66756C]">No change requests for this project.</p>
        @endforelse
    </div>
</x-designer-layout>
