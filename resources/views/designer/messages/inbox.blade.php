<x-designer-layout title="Messages" :flush="true">
    <livewire:messages-inbox :conversation-id="$conversationId" :key="'designer-inbox-'.($conversationId ?? 'list')" />
</x-designer-layout>
