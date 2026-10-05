<x-designer-layout title="Final Design">
    @include('designer.partials.project-header')
    @php
        $files = $concept->files;
        $floor = $files->firstWhere('kind', 'floor_plan');
        $renders = $files->where('kind', 'render');
        $visuals = $files->where('kind', 'visualisation');
        $materials = $project->materialRequirements;
        $changes = $project->designChangeRequests->where('design_impact', 'final_design');
    @endphp
    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="font-serif text-3xl text-[#123D2B]">Final Design</h2>
            <p class="text-sm text-[#66756C]">{{ $concept->statusLabel() }}</p>
        </div>
        @if ($concept->isEditable())
            <form method="POST" action="{{ route('designer.projects.final-design.submit', $project) }}">@csrf<button class="rounded-full bg-[#123D2B] px-4 py-2 text-sm text-white">Send Final Design for Approval</button></form>
        @elseif (in_array($concept->status, [\App\Models\DesignConcept::STATUS_AWAITING, \App\Models\DesignConcept::STATUS_SUBMITTED], true))
            <p class="rounded-full bg-[#E7F0E4] px-4 py-2 text-sm text-[#123D2B]">Sent for Approval @if($concept->submitted_at)<span class="text-[#66756C]">· {{ $concept->submitted_at->format('j M Y') }}</span>@endif</p>
        @elseif ($concept->status === \App\Models\DesignConcept::STATUS_APPROVED)
            <p class="rounded-full bg-[#E7F0E4] px-4 py-2 text-sm font-medium text-[#123D2B]">Approved for Construction</p>
        @endif
    </div>
    @if ($errors->any())
        <p class="mt-3 text-sm text-[#8A3B2A]">{{ $errors->first() }}</p>
    @endif

    <div class="mt-6 grid gap-4 lg:grid-cols-3">
        <section class="rounded-[1.4rem] border border-[#ece7dc] bg-white p-4 shadow-sm lg:col-span-2">
            <h3 class="text-sm font-medium text-[#66756C]">Final architectural images</h3>
            <div class="mt-3 grid grid-cols-2 gap-3">
                @forelse ($renders as $file)
                    <img src="{{ $file->url() }}" alt="" class="h-40 w-full rounded-2xl object-cover">
                @empty
                    <p class="text-sm text-[#66756C]">No final images yet.</p>
                @endforelse
            </div>
            <h3 class="mt-5 text-sm font-medium text-[#66756C]">3D visualisation</h3>
            <div class="mt-3 grid grid-cols-2 gap-3">
                @forelse ($visuals as $file)
                    <img src="{{ $file->url() }}" alt="" class="h-40 w-full rounded-2xl object-cover">
                @empty
                    <p class="text-sm text-[#66756C]">No visualisation uploaded yet.</p>
                @endforelse
            </div>
        </section>
        <section class="rounded-[1.4rem] border border-[#ece7dc] bg-white p-4 shadow-sm">
            <h3 class="text-sm font-medium text-[#66756C]">Floor plan</h3>
            @if ($floor)
                <img src="{{ $floor->url() }}" alt="Floor plan" class="mt-3 h-48 w-full rounded-2xl object-cover">
                <a href="{{ $floor->url() }}" target="_blank" class="mt-2 inline-block text-sm text-[#123D2B] underline">View</a>
            @else
                <p class="mt-3 text-sm text-[#66756C]">No floor plan yet.</p>
            @endif
            @if ($concept->isEditable())
                <form method="POST" action="{{ route('designer.projects.final-design.files', $project) }}" enctype="multipart/form-data" class="mt-4 grid gap-2">
                    @csrf
                    <input type="hidden" name="kind" value="floor_plan">
                    <label class="text-xs text-[#66756C]">Replace floor plan<input type="file" name="image" accept="image/*" required class="mt-1 block w-full text-sm"></label>
                    <button class="rounded-full bg-[#123D2B] px-4 py-2 text-sm text-white">{{ $floor ? 'Replace floor plan' : 'Upload floor plan' }}</button>
                </form>
                <form method="POST" action="{{ route('designer.projects.final-design.files', $project) }}" enctype="multipart/form-data" class="mt-4 grid gap-2 border-t border-[#ece7dc] pt-4">
                    @csrf
                    <label class="text-xs text-[#66756C]">Image type
                        <select name="kind" class="mt-1 w-full rounded-2xl border border-[#ece7dc] px-3 py-2 text-sm">
                            <option value="render">Final image</option>
                            <option value="visualisation">3D visualisation</option>
                        </select>
                    </label>
                    <input type="file" name="image" accept="image/*" required class="text-sm">
                    <button class="rounded-full border border-[#123D2B] px-4 py-2 text-sm text-[#123D2B]">Add image</button>
                </form>
            @endif
        </section>
    </div>

    <section class="mt-6 rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <h3 class="font-serif text-2xl text-[#123D2B]">Material requirements</h3>
            <p class="text-sm text-[#66756C]">{{ $materials->count() }} materials</p>
        </div>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="text-xs text-[#66756C]"><tr><th class="py-2 pr-4">Material</th><th class="py-2 pr-4">Room</th><th class="py-2 pr-4">Quantity</th><th class="py-2 pr-4">Unit</th><th class="py-2">Specification</th></tr></thead>
                <tbody>
                    @forelse ($materials as $material)
                        <tr class="border-t border-[#ece7dc]">
                            <td class="py-2 pr-4 text-[#123D2B]">{{ $material->name }}</td>
                            <td class="py-2 pr-4">{{ $material->room }}</td>
                            <td class="py-2 pr-4">{{ rtrim(rtrim(number_format((float) $material->quantity, 2), '0'), '.') }}</td>
                            <td class="py-2 pr-4">{{ $material->unit }}</td>
                            <td class="py-2">{{ $material->specification }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-3 text-[#66756C]">Materials added here are what the contractor receives after approval.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($concept->isEditable())
            <form method="POST" action="{{ route('designer.projects.final-design.materials', $project) }}" class="mt-4 grid gap-2 md:grid-cols-6">
                @csrf
                <input name="name" required placeholder="Porcelain Tile" class="rounded-2xl border border-[#ece7dc] px-3 py-2 text-sm">
                <input name="room" required placeholder="Living Room" class="rounded-2xl border border-[#ece7dc] px-3 py-2 text-sm">
                <input name="quantity" required type="number" step="0.01" min="0.01" placeholder="450" class="rounded-2xl border border-[#ece7dc] px-3 py-2 text-sm">
                <input name="unit" required placeholder="sq.ft" class="rounded-2xl border border-[#ece7dc] px-3 py-2 text-sm">
                <input name="specification" placeholder="600 × 600 mm" class="rounded-2xl border border-[#ece7dc] px-3 py-2 text-sm">
                <button class="rounded-full bg-[#123D2B] px-4 py-2 text-sm text-white">Add material</button>
            </form>
        @endif
        @if ($concept->notes)
            <p class="mt-4 text-sm text-[#66756C]"><span class="font-medium text-[#123D2B]">Designer notes.</span> {{ $concept->notes }}</p>
        @endif
    </section>

    <section class="mt-6 rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm">
        <h3 class="font-serif text-2xl text-[#123D2B]">Homeowner change requests</h3>
        <ul class="mt-4 space-y-4">
            @forelse ($changes as $change)
                <li class="rounded-2xl bg-[#F7F4EE] p-4">
                    <p class="font-medium text-[#123D2B]">{{ $change->title }}</p>
                    <p class="mt-1 text-xs text-[#66756C]">{{ $change->requester?->name ?? 'Homeowner' }} · {{ $change->created_at?->format('j M Y') }} · {{ $change->statusLabel() }}</p>
                    <p class="mt-2 text-sm text-[#66756C]">{{ $change->description }}</p>
                    @if ($change->status === 'pending')
                        <form method="POST" action="{{ route('designer.final-design.changes.respond', $change) }}" class="mt-3 flex flex-wrap gap-2">
                            @csrf
                            @foreach (['accepted' => 'Accepted', 'rejected' => 'Rejected', 'needs_discussion' => 'Needs Discussion', 'resolved' => 'Resolved'] as $value => $label)
                                <button name="decision" value="{{ $value }}" class="rounded-full border border-[#123D2B] px-3 py-1.5 text-xs text-[#123D2B]">{{ $label }}</button>
                            @endforeach
                        </form>
                    @endif
                </li>
            @empty
                <li class="text-sm text-[#66756C]">No final design change requests yet.</li>
            @endforelse
        </ul>
    </section>
</x-designer-layout>
