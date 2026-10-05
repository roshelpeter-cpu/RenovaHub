<x-designer-layout title="Mood Board">
    @include('designer.partials.project-header')
    @php
        $items = $board->items;
        $photos = $items->filter(fn ($item) => in_array($item->kind, ['inspiration', 'furniture'], true) && $item->imageUrl());
    @endphp
    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="font-serif text-3xl text-[#123D2B]">{{ $board->title }}</h2>
            <p class="text-sm text-[#66756C]">{{ $board->statusLabel() }}@if($board->revision_note) · {{ $board->revision_note }}@endif</p>
        </div>
        @if ($board->canSubmit())
            <form method="POST" action="{{ route('designer.projects.mood-board.submit', $project) }}">@csrf<button class="rounded-full bg-[#123D2B] px-4 py-2 text-sm text-white">{{ $board->status === 'revision_requested' ? 'Resubmit for Approval' : 'Submit for Approval' }}</button></form>
        @elseif ($board->status === \App\Models\MoodBoard::STATUS_AWAITING)
            <div class="rounded-full bg-[#E7F0E4] px-4 py-2 text-sm text-[#123D2B]">
                Sent for Approval
                @if ($board->submitted_at)
                    <span class="text-[#66756C]">· {{ $board->submitted_at->format('j M Y') }}</span>
                @endif
            </div>
        @elseif ($board->isFinal())
            <p class="rounded-full bg-[#E7F0E4] px-4 py-2 text-sm text-[#123D2B]">Approved</p>
        @endif
    </div>
    @if ($errors->any())
        <p class="mt-3 text-sm text-[#8A3B2A]">{{ $errors->first() }}</p>
    @endif
    @if ($board->canSubmit())
        <form method="POST" action="{{ route('designer.projects.mood-board.items.store', $project) }}" enctype="multipart/form-data" class="mt-4 grid gap-3 rounded-[1.4rem] border border-[#ece7dc] bg-white p-4 shadow-sm md:grid-cols-2 xl:grid-cols-6">
            @csrf
            <label class="text-sm">Type
                <select name="kind" class="mt-1 w-full rounded-2xl border border-[#ece7dc] px-3 py-2">
                    @foreach (['inspiration' => 'Inspiration image', 'colour' => 'Colour', 'material' => 'Material', 'furniture' => 'Furniture', 'lighting' => 'Lighting', 'note' => 'Design note'] as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label class="text-sm">Title<input name="title" required class="mt-1 w-full rounded-2xl border border-[#ece7dc] px-3 py-2"></label>
            <label class="text-sm">Note<input name="body" class="mt-1 w-full rounded-2xl border border-[#ece7dc] px-3 py-2"></label>
            <label class="text-sm">Colour<input name="colour" placeholder="#F3EFE7" class="mt-1 w-full rounded-2xl border border-[#ece7dc] px-3 py-2"></label>
            <label class="text-sm">Image<input name="image" type="file" accept="image/*" class="mt-1 w-full text-sm"></label>
            <button class="self-end rounded-full bg-[#123D2B] px-4 py-2 text-sm text-white">Add</button>
        </form>
    @endif
    <div class="mt-6 grid gap-4 lg:grid-cols-3">
        <article class="rounded-[1.4rem] border border-[#ece7dc] bg-white p-4 shadow-sm">
            <h3 class="text-sm font-medium text-[#66756C]">Inspiration Images</h3>
            <div class="mt-3 grid grid-cols-2 gap-2">
                @foreach ($photos as $photo)
                    <figure>
                        <img src="{{ $photo->imageUrl() }}" alt="" class="h-24 w-full rounded-xl object-cover">
                        <figcaption class="mt-1 text-xs text-[#123D2B]">{{ $photo->title }}</figcaption>
                    </figure>
                @endforeach
            </div>
        </article>
        <article class="rounded-[1.4rem] border border-[#ece7dc] bg-white p-4 shadow-sm">
            <h3 class="text-sm font-medium text-[#66756C]">Colour Palette</h3>
            <div class="mt-3 flex flex-wrap gap-3">
                @foreach ($items->where('kind', 'colour') as $swatch)
                    <div class="w-16 text-center">
                        <div class="mx-auto h-10 w-10 rounded-full border border-[#ece7dc]" style="background: {{ $swatch->colour ?: '#F6F1E7' }}"></div>
                        <p class="mt-1 text-[10px] text-[#123D2B]">{{ $swatch->title }}</p>
                    </div>
                @endforeach
            </div>
            <h3 class="mt-5 text-sm font-medium text-[#66756C]">Materials</h3>
            <div class="mt-3 flex flex-wrap gap-3">
                @foreach ($items->where('kind', 'material') as $swatch)
                    <div class="w-16 text-center">
                        <div class="mx-auto h-10 w-14 rounded-lg border border-[#ece7dc]" style="background: {{ $swatch->colour ?: '#E7E1D6' }}"></div>
                        <p class="mt-1 text-[10px] text-[#123D2B]">{{ $swatch->title }}</p>
                    </div>
                @endforeach
            </div>
        </article>
        <article class="rounded-[1.4rem] border border-[#ece7dc] bg-white p-4 shadow-sm">
            <h3 class="text-sm font-medium text-[#66756C]">Furniture & Decor</h3>
            <ul class="mt-3 space-y-2 text-sm text-[#123D2B]">
                @foreach ($items->where('kind', 'furniture') as $piece)
                    <li>{{ $piece->title }}</li>
                @endforeach
            </ul>
            <h3 class="mt-5 text-sm font-medium text-[#66756C]">Lighting</h3>
            <ul class="mt-3 space-y-2 text-sm text-[#123D2B]">
                @foreach ($items->where('kind', 'lighting') as $light)
                    <li>{{ $light->title }}@if($light->body) — {{ $light->body }}@endif</li>
                @endforeach
            </ul>
            <h3 class="mt-5 text-sm font-medium text-[#66756C]">Design Notes</h3>
            <ul class="mt-3 space-y-2 text-sm text-[#66756C]">
                @foreach ($items->where('kind', 'note') as $note)
                    <li><span class="text-[#123D2B]">{{ $note->title }}.</span> {{ $note->body }}</li>
                @endforeach
            </ul>
        </article>
    </div>
    <section class="mt-6 rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm">
        <h3 class="font-serif text-2xl text-[#123D2B]">Homeowner Change Requests</h3>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="text-xs text-[#66756C]">
                    <tr>
                        <th class="py-2 pr-4 font-medium">Request</th>
                        <th class="py-2 pr-4 font-medium">Date</th>
                        <th class="py-2 pr-4 font-medium">Status</th>
                        <th class="py-2 font-medium">Homeowner Comment</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($project->designChangeRequests->where('design_impact', 'mood_board') as $request)
                        <tr class="border-t border-[#ece7dc]">
                            <td class="py-3 pr-4 text-[#123D2B]">{{ $request->title }}</td>
                            <td class="py-3 pr-4 text-[#66756C]">{{ $request->created_at?->format('j M Y') }}</td>
                            <td class="py-3 pr-4">{{ $request->statusLabel() }}</td>
                            <td class="py-3 text-[#66756C]">{{ $request->description }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-3 text-[#66756C]">No homeowner change requests yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</x-designer-layout>
