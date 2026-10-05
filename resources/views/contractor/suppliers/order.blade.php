<x-contractor-layout :title="$order->item">
    <p class="text-sm text-[#66756C]">{{ $order->number }}</p>
    <h1 class="rh-serif text-4xl text-[#123D2B]">{{ $order->item }}</h1>
    <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2 lg:grid-cols-3">
        <div><dt class="text-xs text-[#66756C]">Project</dt><dd class="text-[#123D2B]">{{ $order->project?->name }}</dd></div>
        <div><dt class="text-xs text-[#66756C]">Supplier</dt><dd class="text-[#123D2B]">{{ $order->supplier?->name }}</dd></div>
        <div><dt class="text-xs text-[#66756C]">Amount</dt><dd class="text-[#123D2B]">{{ $order->money() }}</dd></div>
        <div><dt class="text-xs text-[#66756C]">Order date</dt><dd class="text-[#123D2B]">{{ $order->ordered_at?->format('j M Y') }}</dd></div>
        <div><dt class="text-xs text-[#66756C]">Expected delivery</dt><dd class="text-[#123D2B]">{{ $order->required_delivery_date?->format('j M Y') ?? '—' }}</dd></div>
        <div><dt class="text-xs text-[#66756C]">Payment</dt><dd class="text-[#123D2B]">{{ $order->payment?->statusLabel() ?? 'Pending' }}</dd></div>
    </dl>
    <ol class="mt-6 grid gap-2 sm:grid-cols-3 lg:grid-cols-6">
        @foreach (\App\Models\SupplierOrder::PIPELINE as $index => $step)
            @php $state = $index < $order->pipelineIndex() ? 'done' : ($index === $order->pipelineIndex() ? 'current' : 'upcoming'); @endphp
            <li class="rounded-2xl border px-3 py-3 text-sm {{ $state === 'upcoming' ? 'border-[#ece7dc] text-[#66756C]' : 'border-[#123D2B] bg-white text-[#123D2B]' }}">
                {{ match ($step) {
                    'price_received' => 'Price Received',
                    'payment_pending' => 'Payment Pending',
                    'paid' => 'Payment Received',
                    'processing' => 'Processing',
                    'dispatched' => 'Dispatched',
                    default => 'Delivered',
                } }}
            </li>
        @endforeach
    </ol>
    @if ($order->contractorTransitions() !== [])
        <form method="POST" action="{{ route('contractor.orders.update', $order) }}" class="mt-6 flex items-center gap-2">
            @csrf
            @method('PUT')
            <select name="status" class="rounded-full border border-[#ddd6c8] bg-white px-3 py-2 text-sm">
                @foreach ($order->contractorTransitions() as $status)
                    <option value="{{ $status }}">{{ str_replace('_', ' ', ucfirst($status)) }}</option>
                @endforeach
            </select>
            <button class="rounded-full bg-[#123D2B] px-4 py-2 text-sm text-white">Update fulfilment</button>
        </form>
    @else
        <p class="mt-6 text-sm text-[#66756C]">Payment stays pending until PayHere confirms it. You cannot mark this order as paid.</p>
    @endif
    @if ($errors->any())
        <ul class="mt-3 text-sm text-red-700">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    @endif
</x-contractor-layout>
