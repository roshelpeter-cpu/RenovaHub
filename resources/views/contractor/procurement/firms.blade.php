<x-contractor-layout title="Compare firms">
    <h1 class="rh-serif text-3xl text-[#123D2B]">Construction firm comparison</h1>
    <p class="mt-2 text-sm text-[#66756C]">{{ $project->name }}. The homeowner chooses the firm before you can assign it.</p>
    @if ($quotes->count() < 2)
        <p class="mt-6 rounded-2xl border border-[#ece7dc] bg-white p-6 text-sm text-[#66756C]">Ask at least two firms for a price, duration and scope. <a href="{{ route('contractor.firms.index') }}" class="underline">Find construction firms</a></p>
    @else
        <form method="POST" action="{{ route('contractor.procurement.firms.send', $project) }}" class="mt-6">
            @csrf
            <div class="grid gap-4 lg:grid-cols-3">
                @foreach ($quotes as $quote)
                    <label class="block rounded-2xl border border-[#ece7dc] bg-white p-5 shadow-sm">
                        <span class="flex items-center justify-between gap-3">
                            <span class="rh-serif text-xl text-[#123D2B]">{{ $quote->firm->name }}</span>
                            <input type="checkbox" name="quotes[]" value="{{ $quote->id }}" class="h-4 w-4" @disabled($quote->status !== \App\Models\ConstructionFirmQuotation::STATUS_RECEIVED) @checked($quote->status === \App\Models\ConstructionFirmQuotation::STATUS_RECEIVED)>
                        </span>
                        <span class="mt-3 block text-2xl font-semibold">{{ $quote->money() }}</span>
                        <span class="mt-1 block text-sm text-[#66756C]">{{ $quote->duration_days }} days · {{ $quote->statusLabel() }}</span>
                        <dl class="mt-4 space-y-2 text-sm text-[#66756C]">
                            <div class="flex justify-between"><dt>Start</dt><dd>{{ $quote->start_date?->format('j M Y') }}</dd></div>
                            <div class="flex justify-between"><dt>Completion</dt><dd>{{ $quote->completion_date?->format('j M Y') }}</dd></div>
                            <div class="flex justify-between"><dt>Rating</dt><dd>{{ $quote->firm->rating }}</dd></div>
                            <div class="flex justify-between"><dt>Warranty</dt><dd>{{ $quote->warranty ?: '—' }}</dd></div>
                        </dl>
                        <p class="mt-3 text-sm text-[#66756C]">{{ $quote->scope }}</p>
                    </label>
                @endforeach
            </div>
            <button class="mt-5 rounded-xl bg-[#123D2B] px-5 py-2.5 text-sm text-white">Send Options to Homeowner</button>
        </form>
    @endif
</x-contractor-layout>
