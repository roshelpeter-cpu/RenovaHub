<x-homeowner-layout title="Messages" :flush="true">
    <livewire:messages-inbox :conversation-id="$conversationId" :key="'inbox-'.($conversationId ?? 'list')" />
</x-homeowner-layout>
