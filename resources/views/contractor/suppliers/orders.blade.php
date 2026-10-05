<x-contractor-layout title="Supplier Orders">
    <h1 class="rh-serif text-4xl text-[#123D2B]">Supplier Orders</h1>
    <p class="mt-2 text-sm text-[#66756C]">Orders stay under Suppliers. Payment is requested from the homeowner and is not marked paid here.</p>
    <div class="mt-6 overflow-x-auto rounded-[1.4rem] border border-[#ece7dc] bg-white shadow-sm">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-[#F6F1E7] text-xs uppercase tracking-wide text-[#66756C]"><tr><th class="px-4 py-3">Order</th><th class="px-4 py-3">Project</th><th class="px-4 py-3">Supplier</th><th class="px-4 py-3">Amount</th><th class="px-4 py-3">Status</th><th class="px-4 py-3"></th></tr></thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr class="border-t border-[#ece7dc]">
                        <td class="px-4 py-3 text-[#123D2B]">{{ $order->item }}</td>
                        <td class="px-4 py-3">{{ $order->project?->name }}</td>
                        <td class="px-4 py-3">{{ $order->supplier?->name }}</td>
                        <td class="px-4 py-3">{{ $order->money() }}</td>
                        <td class="px-4 py-3">{{ $order->statusLabel() }}</td>
                        <td class="px-4 py-3"><a class="underline" href="{{ route('contractor.orders.show', $order) }}">View</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-[#66756C]">No supplier orders yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-contractor-layout>
