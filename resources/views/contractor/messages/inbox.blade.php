<x-contractor-layout title="Messages" :flush="true">
    <livewire:messages-inbox :conversation-id="$conversationId" :key="'contractor-inbox-'.($conversationId ?? 'list')" />
</x-contractor-layout>
