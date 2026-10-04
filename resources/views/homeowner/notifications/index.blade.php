<x-homeowner-layout title="Notifications">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="font-serif text-3xl text-forest sm:text-4xl">Notifications</h1>
            <p class="mt-2 text-sm text-mist">Updates from your projects.</p>
        </div>
        <form method="POST" action="{{ route('homeowner.notifications.read-all') }}">@csrf<button class="text-sm font-medium text-forest hover:underline">Mark all as read</button></form>
    </div>
    <div class="mt-4 flex flex-wrap gap-2">
        @foreach (['all' => 'All', 'unread' => 'Unread', 'projects' => 'Projects', 'quotations' => 'Quotations', 'payments' => 'Payments', 'messages' => 'Messages', 'design' => 'Design'] as $value => $label)
            <a href="{{ route('homeowner.notifications.index', ['filter' => $value]) }}" class="rounded-full px-4 py-2 text-sm {{ $filter === $value ? 'bg-forest text-ivory' : 'bg-white text-charcoal' }}">{{ $label }}</a>
        @endforeach
    </div>
    @if ($notifications->isEmpty())
        <div class="mt-6">@include('homeowner.partials.empty', ['title' => 'No notifications', 'body' => 'Invitations, quotations and messages will show up here.'])</div>
    @else
        <ul class="mt-6 space-y-3">
            @foreach ($notifications as $notification)
                <li class="rounded-3xl border border-[#ece7dc] bg-white p-4 {{ $notification->read_at ? '' : 'ring-1 ring-[#DCE7D8]' }}">
                    <form method="POST" action="{{ route('homeowner.notifications.read', $notification->id) }}">
                        @csrf
                        <button class="text-left">
                            <p class="font-medium text-charcoal">{{ $notification->data['title'] ?? 'Update' }}</p>
                            <p class="mt-1 text-sm text-mist">{{ $notification->data['body'] ?? '' }}</p>
                            <p class="mt-1 text-xs uppercase tracking-[0.12em] text-olive">{{ $notification->created_at->diffForHumans() }}</p>
                        </button>
                    </form>
                </li>
            @endforeach
        </ul>
        <div class="mt-6">{{ $notifications->links() }}</div>
    @endif
</x-homeowner-layout>
