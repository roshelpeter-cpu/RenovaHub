<x-contractor-layout title="Earnings">
    <h1 class="rh-serif text-4xl text-[#123D2B]">Earnings</h1>
    <p class="mt-2 max-w-2xl text-sm text-[#66756C]">Track project payments and payment allocations.</p>

    @if ($projects->isEmpty())
        <div class="mt-6 rounded-[1.4rem] border border-[#ece7dc] bg-white p-8 text-sm text-[#66756C] shadow-sm">
            Project payments appear here after a final budget is approved.
        </div>
    @else
        <div class="mt-6 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($projects as $project)
                @php
                    $payment = $project->payments->first();
                    $paid = $payment?->status === \App\Models\Payment::STATUS_PAID;
                @endphp
                <article class="overflow-hidden rounded-[1.4rem] border border-[#ece7dc] bg-white shadow-sm">
                    <img src="{{ $project->coverUrl() ?: asset('images/renova/about-exterior.jpg') }}" alt="" class="h-44 w-full object-cover">
                    <div class="p-5">
                        <h2 class="rh-serif text-2xl text-[#123D2B]">{{ $project->name }}</h2>
                        <dl class="mt-4 space-y-2 text-sm">
                            <div class="flex justify-between gap-3">
                                <dt class="text-[#66756C]">Homeowner</dt>
                                <dd class="text-right text-[#123D2B]">{{ $project->homeowner?->name ?: 'Homeowner' }}</dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt class="text-[#66756C]">Total Project Payment</dt>
                                <dd class="text-right text-[#123D2B]">{{ $payment ? $payment->formatMoney($payment->renovationAmount()) : '—' }}</dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt class="text-[#66756C]">Payment Status</dt>
                                <dd class="text-right font-medium {{ $paid ? 'text-[#0E8A4C]' : 'text-[#9A6B12]' }}">{{ $paid ? 'Paid' : 'Pending' }}</dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt class="text-[#66756C]">Date</dt>
                                <dd class="text-right text-[#123D2B]">{{ ($payment?->paid_at ?? $payment?->created_at)?->format('j M Y') ?: '—' }}</dd>
                            </div>
                        </dl>
                        <a href="{{ route('contractor.earnings.project', $project) }}" class="mt-5 inline-flex rounded-full bg-[#123D2B] px-4 py-2.5 text-sm font-medium text-white">View Payment Details</a>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</x-contractor-layout>
