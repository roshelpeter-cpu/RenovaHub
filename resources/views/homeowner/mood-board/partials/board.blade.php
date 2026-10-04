@php
    $board = $project->moodBoard;
    $items = $board?->items ?? collect();
    $photos = $items->filter(fn ($item) => in_array($item->kind, ['inspiration', 'image', 'furniture'], true) && $item->imageUrl());
    $colours = $items->where('kind', 'colour');
    $materials = $items->where('kind', 'material');
    $furniture = $items->where('kind', 'furniture');
    $visible = $photos->take(4);
    $extra = max(0, $photos->count() - 4);
    $completed = $project->isCompleted();
    $author = $board?->author?->professionalProfile?->displayName() ?? $board?->author?->name ?? $project->designer?->professionalProfile?->displayName();
@endphp

<div class="grid items-start gap-5 {{ empty($embedded) ? 'xl:grid-cols-12' : 'lg:grid-cols-2' }}">
    @if (empty($embedded))
    <div class="xl:col-span-3">
        <div class="overflow-hidden rounded-2xl">
            <div class="relative">
                @if ($project->coverUrl())
                    <img src="{{ $project->coverUrl() }}" alt="" class="h-44 w-full object-cover">
                @endif
                <span class="absolute left-3 top-3 inline-flex items-center gap-1 rounded-full bg-white/95 px-2.5 py-1 text-xs font-medium text-[#123D2B]">
                    <span class="h-1.5 w-1.5 rounded-full {{ $completed ? 'bg-[#2F6B49]' : 'bg-[#C4A15A]' }}"></span>
                    {{ $completed ? 'Completed' : 'In Progress' }}
                </span>
            </div>
        </div>
        <h3 class="mt-3 font-serif text-xl text-[#123D2B]">{{ $project->name }}</h3>
        <p class="mt-2 flex flex-wrap gap-x-3 gap-y-1 text-xs text-[#66756C]">
            <span>{{ $project->cataloguePlaceLabel() }}</span>
            <span>{{ $project->displayTypeLabel() }}</span>
        </p>
        <p class="mt-2 text-sm leading-relaxed text-[#66756C]">{{ \Illuminate\Support\Str::limit($project->description, 140) }}</p>
        <a href="{{ route('homeowner.projects.show', $project) }}" class="mt-3 inline-flex text-sm font-medium text-[#123D2B] hover:underline">View Project →</a>
    </div>
    @endif

    <div class="{{ empty($embedded) ? 'xl:col-span-4' : '' }}">
        <h3 class="font-serif text-lg text-[#123D2B]">{{ $completed ? 'Final Design Mood Board' : 'Design Mood Board' }}</h3>
        <p class="text-xs text-[#66756C]">{{ $board?->summary ?: 'Current design direction' }}</p>
        <p class="mt-3 text-xs font-medium text-[#66756C]">Inspiration Images</p>
        <div class="mt-2 grid grid-cols-3 gap-2">
            @foreach ($visible as $photo)
                <div class="relative overflow-hidden rounded-xl">
                    <img src="{{ $photo->imageUrl() }}" alt="{{ $photo->title }}" class="h-20 w-full object-cover sm:h-24">
                    @if ($loop->last && $extra > 0)
                        <span class="absolute inset-0 flex items-center justify-center bg-black/45 text-sm font-medium text-white">+{{ $extra }} More Images</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <div class="{{ empty($embedded) ? 'xl:col-span-5' : '' }} space-y-4">
        <div class="flex flex-wrap items-center gap-2 text-xs text-[#66756C]">
            @if ($completed)
                <span class="rounded-full bg-[#E7F0E4] px-2.5 py-1 text-[#2F6B49]">Homeowner Approved</span>
                <span class="rounded-full bg-[#E7F0E4] px-2.5 py-1 text-[#2F6B49]">Final Design</span>
                <span>Completed · {{ ($project->actual_completion_date ?? $project->expected_completion_date)?->format('j M Y') }}</span>
            @else
                <span class="rounded-full bg-[#F3EFE4] px-2.5 py-1 text-[#123D2B]">Design In Progress</span>
                @if ($board)
                    <span>Last Updated · {{ $board->updated_at->format('j M Y') }}</span>
                @endif
                @if ($author)
                    <span>Uploaded by {{ $author }}</span>
                @endif
            @endif
        </div>
        @if ($colours->isNotEmpty())
            <div>
                <p class="text-xs font-medium text-[#66756C]">Colour Palette</p>
                <div class="mt-2 flex flex-wrap gap-3">
                    @foreach ($colours as $swatch)
                        <div class="w-14 text-center">
                            <div class="mx-auto h-10 w-10 rounded-full border border-[#ece7dc]" style="background: {{ $swatch->colour ?: '#F6F1E7' }}"></div>
                            <p class="mt-1 text-[10px] leading-tight text-[#123D2B]">{{ $swatch->title }}</p>
                            @if ($swatch->colour)<p class="text-[10px] text-[#66756C]">{{ $swatch->colour }}</p>@endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        @if ($materials->isNotEmpty())
            <div>
                <p class="text-xs font-medium text-[#66756C]">Materials</p>
                <div class="mt-2 flex flex-wrap gap-3">
                    @foreach ($materials as $swatch)
                        <div class="w-16 text-center">
                            <div class="mx-auto h-10 w-14 rounded-lg border border-[#ece7dc]" style="background: {{ $swatch->colour ?: '#E7E1D6' }}"></div>
                            <p class="mt-1 text-[10px] leading-tight text-[#123D2B]">{{ $swatch->title }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        @if ($furniture->isNotEmpty())
            <div>
                <p class="text-xs font-medium text-[#66756C]">Furniture & Decor</p>
                <div class="mt-2 flex flex-wrap gap-2">
                    @foreach ($furniture as $piece)
                        <div class="w-16 text-center">
                            @if ($piece->imageUrl())
                                <img src="{{ $piece->imageUrl() }}" alt="" class="h-12 w-full rounded-lg object-cover">
                            @else
                                <div class="h-12 rounded-lg bg-[#F6F1E7]"></div>
                            @endif
                            <p class="mt-1 text-[10px] leading-tight text-[#123D2B]">{{ $piece->title }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
