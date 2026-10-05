<x-homeowner-layout :title="$project->name.' final design'" :flush="true" :canvas="true">
    <x-project-context :project="$project" section="final-design">
        @php
            $files = $concept?->files ?? collect();
            $materials = $concept ? $project->materialRequirements->where('design_concept_id', $concept->id) : collect();
            $changes = $project->designChangeRequests->where('design_impact', 'final_design');
        @endphp
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="font-serif text-3xl text-[#123D2B]">Final Design</h2>
                <p class="mt-1 text-sm text-[#66756C]">{{ $concept?->statusLabel() ?? 'The designer has not sent a final design yet.' }}</p>
            </div>
            @if ($concept?->status === \App\Models\DesignConcept::STATUS_APPROVED)
                <p class="rounded-full bg-[#E7F0E4] px-4 py-2 text-sm font-medium text-[#123D2B]">Approved for Construction</p>
            @endif
        </div>

        @if ($concept?->status === \App\Models\DesignConcept::STATUS_AWAITING)
            <div class="mt-4 flex flex-wrap gap-3 rounded-2xl border border-[#ece7dc] bg-white p-4">
                <form method="POST" action="{{ route('homeowner.projects.final-design.approve', $project) }}">@csrf<button class="rounded-full bg-[#123D2B] px-4 py-2 text-sm text-white">Approve Final Design</button></form>
                <button type="button" class="rounded-full border border-[#123D2B] px-4 py-2 text-sm text-[#123D2B]" onclick="document.getElementById('final-change').showModal()">Request Changes</button>
            </div>
            <dialog id="final-change" class="w-full max-w-lg rounded-3xl border border-[#ece7dc] p-6 backdrop:bg-[#123D2B]/40">
                <form method="POST" action="{{ route('homeowner.projects.final-design.request-changes', $project) }}" class="grid gap-3">
                    @csrf
                    <h3 class="font-serif text-2xl text-[#123D2B]">Request Changes</h3>
                    <label class="text-sm text-[#66756C]">Change request<input name="title" required class="mt-1 w-full rounded-2xl border border-[#ece7dc] px-3 py-2" placeholder="Change countertop finish"></label>
                    <label class="text-sm text-[#66756C]">Comment<textarea name="comment" required minlength="8" class="mt-1 w-full rounded-2xl border border-[#ece7dc] px-3 py-2"></textarea></label>
                    <div class="flex justify-end gap-2">
                        <button type="button" class="rounded-full border border-[#c9c2b4] px-4 py-2 text-sm" onclick="document.getElementById('final-change').close()">Cancel</button>
                        <button class="rounded-full bg-[#123D2B] px-4 py-2 text-sm text-white">Submit</button>
                    </div>
                </form>
            </dialog>
        @elseif ($concept?->status === \App\Models\DesignConcept::STATUS_REVISION)
            <p class="mt-4 rounded-2xl bg-[#F6F1E7] px-4 py-3 text-sm text-[#123D2B]">Changes Requested</p>
        @endif

        @if (! $concept)
            <div class="mt-6">@include('homeowner.partials.empty', ['title' => 'No final design yet', 'body' => 'The designer will send the floor plan, visuals and material list here.'])</div>
        @else
            <div class="mt-6 grid gap-4 lg:grid-cols-3">
                <section class="rounded-[1.4rem] border border-[#ece7dc] bg-white p-4 shadow-sm lg:col-span-2">
                    <h3 class="text-sm font-medium text-[#66756C]">Final images</h3>
                    <div class="mt-3 grid grid-cols-2 gap-3">
                        @foreach ($files->whereIn('kind', ['render', 'visualisation']) as $file)
                            <figure>
                                <img src="{{ $file->url() }}" alt="" class="h-40 w-full rounded-2xl object-cover">
                                <figcaption class="mt-1 text-xs text-[#66756C]">{{ $file->kind === 'visualisation' ? '3D visualisation' : 'Architectural image' }}</figcaption>
                            </figure>
                        @endforeach
                    </div>
                </section>
                <section class="rounded-[1.4rem] border border-[#ece7dc] bg-white p-4 shadow-sm">
                    <h3 class="text-sm font-medium text-[#66756C]">Floor plan</h3>
                    @php $floor = $files->firstWhere('kind', 'floor_plan'); @endphp
                    @if ($floor)
                        <img src="{{ $floor->url() }}" alt="Floor plan" class="mt-3 h-48 w-full rounded-2xl object-cover">
                        <a href="{{ $floor->url() }}" target="_blank" class="mt-2 inline-block text-sm text-[#123D2B] underline">Preview</a>
                    @else
                        <p class="mt-3 text-sm text-[#66756C]">Floor plan will appear with the package.</p>
                    @endif
                </section>
            </div>
            <section class="mt-6 rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm">
                <h3 class="font-serif text-2xl text-[#123D2B]">Materials · {{ $materials->count() }}</h3>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="text-xs text-[#66756C]"><tr><th class="py-2 pr-4">Material</th><th class="py-2 pr-4">Room</th><th class="py-2 pr-4">Quantity</th><th class="py-2 pr-4">Unit</th><th class="py-2">Specification</th></tr></thead>
                        <tbody>
                            @foreach ($materials as $material)
                                <tr class="border-t border-[#ece7dc]">
                                    <td class="py-2 pr-4">{{ $material->name }}</td>
                                    <td class="py-2 pr-4">{{ $material->room }}</td>
                                    <td class="py-2 pr-4">{{ rtrim(rtrim(number_format((float) $material->quantity, 2), '0'), '.') }}</td>
                                    <td class="py-2 pr-4">{{ $material->unit }}</td>
                                    <td class="py-2">{{ $material->specification }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($concept->notes)
                    <p class="mt-4 text-sm text-[#66756C]">{{ $concept->notes }}</p>
                @endif
            </section>
            <section class="mt-6 rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm">
                <h3 class="font-serif text-2xl text-[#123D2B]">Change requests</h3>
                <ul class="mt-3 space-y-3 text-sm">
                    @forelse ($changes as $change)
                        <li>
                            <p class="font-medium text-[#123D2B]">{{ $change->title }}</p>
                            <p class="text-[#66756C]">{{ $change->created_at?->format('j M Y') }} · {{ $change->statusLabel() }}</p>
                            <p class="text-[#66756C]">{{ $change->description }}</p>
                        </li>
                    @empty
                        <li class="text-[#66756C]">No change requests.</li>
                    @endforelse
                </ul>
            </section>
        @endif
    </x-project-context>
</x-homeowner-layout>
