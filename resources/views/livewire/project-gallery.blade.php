<div id="{{ $showOverviewGrid ? 'project-gallery-grid' : 'project-gallery' }}">
    @if ($count === 0)
        <div class="flex h-40 items-center justify-center rounded-[1.4rem] bg-[#EFE8DA] text-sm text-[#66756C]">No project photos yet.</div>
    @elseif ($showOverviewGrid)
        <section>
            <div class="mb-4 flex items-end justify-between">
                <h2 class="font-serif text-2xl text-[#123D2B]">Project Gallery</h2>
                <a href="#project-gallery" wire:click="select(0)" class="text-sm font-medium text-[#123D2B]">View All →</a>
            </div>
            <div class="grid grid-cols-2 gap-3">
                @foreach ($images->take(4) as $thumbIndex => $image)
                    <button type="button" wire:click="select({{ $thumbIndex }})" class="overflow-hidden rounded-2xl text-left">
                        <img src="{{ $image->url() }}" alt="{{ $image->original_name }}" class="h-28 w-full object-cover sm:h-36">
                    </button>
                @endforeach
            </div>
        </section>
    @else
        <div class="grid gap-3 sm:grid-cols-3">
            <div class="relative overflow-hidden rounded-[1.4rem] sm:col-span-2">
                <img src="{{ $current->url() }}" alt="{{ $current->original_name }}" class="h-64 w-full object-cover sm:h-[22rem]">
                <span class="absolute left-4 top-4 inline-flex items-center gap-1.5 rounded-full bg-[#E7F0E4] px-3 py-1 text-xs font-medium text-[#123D2B]">
                    @if ($project->status === 'completed')
                        <svg viewBox="0 0 16 16" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m3.5 8.2 3 3 6-6.4"/></svg>
                        Completed
                    @else
                        <span class="h-1.5 w-1.5 rounded-full bg-[#123D2B]"></span>
                        In Progress
                    @endif
                </span>
                <span class="absolute bottom-3 left-3 rounded-full bg-black/50 px-2.5 py-1 text-xs text-white">{{ $index + 1 }}/{{ $count }}</span>
                <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <button type="button" wire:click="previous" class="flex h-9 w-9 items-center justify-center rounded-full bg-white/90 text-lg text-[#123D2B] shadow-sm" aria-label="Previous image">‹</button>
                </div>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <button type="button" wire:click="next" class="flex h-9 w-9 items-center justify-center rounded-full bg-white/90 text-lg text-[#123D2B] shadow-sm" aria-label="Next image">›</button>
                </div>
            </div>
            <div class="grid grid-cols-4 gap-2 sm:grid-cols-1">
                @foreach ($images->take(4) as $thumbIndex => $image)
                    <button type="button" wire:click="select({{ $thumbIndex }})" class="relative overflow-hidden rounded-2xl {{ $index === $thumbIndex ? 'ring-2 ring-[#123D2B]' : '' }}" aria-label="Show photo {{ $thumbIndex + 1 }}">
                        <img src="{{ $image->url() }}" alt="" class="h-16 w-full object-cover sm:h-[4.85rem]">
                        @if ($thumbIndex === 3 && $count > 4)
                            <span class="absolute inset-0 flex items-center justify-center bg-black/50 px-2 text-center text-xs font-medium text-white">+{{ $count - 4 }} More Photos</span>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>
    @endif
</div>
