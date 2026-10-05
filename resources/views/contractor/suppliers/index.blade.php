<x-contractor-layout title="Suppliers">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="rh-serif text-4xl text-[#123D2B] sm:text-5xl">Suppliers</h1>
            <p class="mt-2 text-sm text-[#66756C]">Find trusted suppliers, view product ranges and place orders for your projects.</p>
        </div>
        <a href="{{ route('contractor.orders.index') }}" class="text-sm text-[#123D2B] underline">Supplier orders</a>
    </div>
    <div class="mt-6">
        <livewire:supplier-browser />
    </div>
</x-contractor-layout>
