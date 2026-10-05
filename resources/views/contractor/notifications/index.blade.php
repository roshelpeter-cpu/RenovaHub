<x-contractor-layout title="Notifications">
    <h1 class="rh-serif text-4xl text-[#123D2B]">Notifications</h1>
    <ul class="mt-6 space-y-3">
        @forelse ($notifications as $notification)
            <li class="rounded-2xl border border-[#ece7dc] bg-white p-4 {{ $notification->read_at ? 'opacity-70' : '' }}">
                <form method="POST" action="{{ route('contractor.notifications.read', $notification->id) }}">
                    @csrf
                    <button class="text-left">
                        <p class="font-medium text-[#123D2B]">{{ $notification->data['title'] ?? 'Update' }}</p>
                        <p class="text-sm text-[#66756C]">{{ $notification->data['body'] ?? '' }}</p>
                    </button>
                </form>
            </li>
        @empty
            <li class="text-sm text-[#66756C]">No notifications yet.</li>
        @endforelse
    </ul>
</x-contractor-layout>
