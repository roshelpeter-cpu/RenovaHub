<x-designer-layout title="Messages">
    @include('designer.partials.project-header')
    <div class="mt-4 overflow-hidden rounded-[1.4rem] border border-[#ece7dc]">
        <livewire:messages-inbox :conversation-id="$conversationId" :key="'designer-project-'.$project->id" />
    </div>
</x-designer-layout>
