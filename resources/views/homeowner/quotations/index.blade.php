<x-homeowner-layout title="Quotations">
    @if ($project) @include('homeowner.projects.partials.tabs', ['project' => $project]) @endif
    <h1 class="mt-4 font-serif text-3xl text-forest sm:text-4xl">Quotations</h1>
    <p class="mt-2 text-sm text-mist">Review contractor pricing before you approve it.</p>
    @if ($quotations->isEmpty())
        <div class="mt-6">@include('homeowner.partials.empty', ['title' => 'No quotations yet', 'body' => 'Contractor quotations will appear here when they are submitted.'])</div>
    @else
        <div class="mt-6 grid gap-4">
            @foreach ($quotations as $quotation)
                <article class="rounded-3xl border border-[#ece7dc] bg-white p-5 shadow-sm">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-[0.14em] text-olive">{{ $quotation->number }} · {{ $quotation->project->name }}</p>
                            <h2 class="mt-1 font-serif text-2xl text-forest">{{ $quotation->money($quotation->total) }}</h2>
                            <p class="mt-1 text-sm text-mist">{{ $quotation->project->name }} · {{ $quotation->statusLabel() }}</p>
                            <p class="mt-1 text-sm text-mist">Submitted {{ $quotation->created_at->format('j M Y') }}@if ($quotation->approved_at) · Approved {{ $quotation->approved_at->format('j M Y') }}@endif</p>
                            <p class="mt-2 text-sm text-charcoal">{{ $quotation->description }}</p>
                        </div>
                        <a href="{{ route('homeowner.quotations.show', [$quotation->project, $quotation]) }}" class="rounded-full border border-forest px-4 py-2 text-sm font-medium text-forest">View quotation</a>
                    </div>
                </article>
            @endforeach
        </div>
        <div class="mt-6">{{ $quotations->links() }}</div>
    @endif
</x-homeowner-layout>
