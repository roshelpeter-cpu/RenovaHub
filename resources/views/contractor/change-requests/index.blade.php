<x-contractor-layout title="Change Requests">
    <h1 class="rh-serif text-4xl text-[#123D2B]">Change Requests</h1>
    <p class="mt-2 text-sm text-[#66756C]">You review cost and construction impact. The homeowner approves or rejects the change.</p>
    <div class="mt-6 space-y-3">
        @forelse ($changes as $change)
            <a href="{{ route('contractor.change-requests.show', $change) }}" class="block rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <h2 class="rh-serif text-2xl text-[#123D2B]">{{ $change->title }}</h2>
                    <span class="rounded-full bg-[#F6F1E7] px-3 py-1 text-xs text-[#123D2B]">{{ $change->statusLabel() }}</span>
                </div>
                <p class="mt-2 text-sm text-[#66756C]">{{ $change->project?->name }} · {{ $change->requester?->name }}</p>
                <p class="mt-2 text-sm text-[#66756C]">{{ \Illuminate\Support\Str::limit($change->description, 180) }}</p>
            </a>
        @empty
            <p class="text-sm text-[#66756C]">No change requests on your projects.</p>
        @endforelse
    </div>
</x-contractor-layout>
