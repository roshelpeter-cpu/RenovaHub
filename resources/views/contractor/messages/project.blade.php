<x-contractor-layout title="Messages">
    @include('contractor.partials.project-header', ['project' => $project, 'section' => 'messages'])
    <div class="mt-4 overflow-hidden rounded-[1.4rem] border border-[#ece7dc]">
        <livewire:messages-inbox :conversation-id="$conversationId" :key="'contractor-project-'.($conversationId ?? $project->id)" />
    </div>
</x-contractor-layout>
