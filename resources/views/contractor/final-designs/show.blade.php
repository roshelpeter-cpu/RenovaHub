<x-contractor-layout :title="$project->name">
    <p class="text-sm text-[#66756C]"><a href="{{ route('contractor.final-designs.index') }}" class="underline">Final Designs</a> / {{ $project->name }}</p>
    <div class="mt-3 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="rh-serif text-3xl text-[#123D2B]">Final Design Package</h1>
            <p class="mt-2 text-sm text-[#66756C]">Approved {{ $concept->approved_at?->format('j M Y') }} by the homeowner. This package is read-only.</p>
        </div>
        <a href="{{ route('contractor.materials.index', ['project' => $project->id]) }}" class="rounded-xl border border-[#ddd6c8] bg-white px-4 py-2 text-sm text-[#123D2B]">Material requirements</a>
    </div>

    <section class="mt-6">
        <h2 class="rh-serif text-2xl text-[#123D2B]">Final design images</h2>
        <div class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($concept->files as $file)
                <figure class="overflow-hidden rounded-2xl border border-[#ece7dc] bg-white">
                    <img src="{{ $file->url() }}" alt="{{ $file->caption }}" class="h-44 w-full object-cover">
                    <figcaption class="px-3 py-2 text-sm text-[#66756C]">{{ $file->caption ?: ucfirst($file->kind) }}</figcaption>
                </figure>
            @empty
                <img src="{{ $project->coverUrl() ?: asset('images/renova/feature-plans.jpg') }}" alt="" class="h-44 w-full rounded-2xl object-cover">
            @endforelse
        </div>
    </section>

    <section class="mt-6 grid gap-4 lg:grid-cols-2">
        <article class="rounded-2xl border border-[#ece7dc] bg-white p-5 shadow-sm">
            <h2 class="rh-serif text-2xl text-[#123D2B]">Designer notes</h2>
            <p class="mt-3 text-sm leading-relaxed text-[#66756C]">{{ $concept->notes ?: $concept->description }}</p>
        </article>
        <article class="rounded-2xl border border-[#ece7dc] bg-white p-5 shadow-sm">
            <h2 class="rh-serif text-2xl text-[#123D2B]">Approval history</h2>
            <ul class="mt-3 space-y-2 text-sm">
                @foreach ($history as $row)
                    <li class="flex justify-between gap-3 border-b border-[#ece7dc] py-2">
                        <span class="text-[#123D2B]">{{ $row->title }}</span>
                        <span class="text-[#66756C]">{{ $row->statusLabel() }}</span>
                    </li>
                @endforeach
            </ul>
        </article>
    </section>

    <section class="mt-6">
        <h2 class="rh-serif text-2xl text-[#123D2B]">Material requirements</h2>
        <div class="mt-3 overflow-x-auto rounded-2xl border border-[#ece7dc] bg-white shadow-sm">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-[#F6F1E7] text-xs uppercase tracking-wide text-[#66756C]"><tr><th class="px-4 py-3">Material</th><th class="px-4 py-3">Room</th><th class="px-4 py-3">Quantity</th><th class="px-4 py-3">Specification</th><th class="px-4 py-3">Status</th></tr></thead>
                <tbody>
                    @forelse ($materials as $item)
                        <tr class="border-t border-[#ece7dc]">
                            <td class="px-4 py-3 text-[#123D2B]">{{ $item->name }}</td>
                            <td class="px-4 py-3">{{ $item->room }}</td>
                            <td class="px-4 py-3">{{ rtrim(rtrim(number_format((float) $item->quantity, 2), '0'), '.') }} {{ $item->unit }}</td>
                            <td class="px-4 py-3 text-[#66756C]">{{ $item->specification }}</td>
                            <td class="px-4 py-3">{{ ucfirst($item->status) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-6 text-[#66756C]">No material list is stored for this approved package yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</x-contractor-layout>
