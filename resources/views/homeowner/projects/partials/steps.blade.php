@php
    $steps = [
        1 => 'Project Details',
        2 => 'Location',
        3 => 'Budget & Timeline',
        4 => 'Select Team',
    ];
    $project = $project ?? null;
@endphp

<ol class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4" aria-label="Project setup">
    @foreach ($steps as $number => $label)
        @php
            $state = $number === $current ? 'active' : ($number < $current ? 'done' : 'upcoming');
            $href = null;
            if ($project && $state === 'done') {
                $href = match ($number) {
                    1 => route('homeowner.projects.edit', $project),
                    2 => route('homeowner.projects.location', $project),
                    3 => route('homeowner.projects.budget', $project),
                    default => null,
                };
            }
        @endphp
        <li class="rounded-2xl border px-3 py-3 {{ $state === 'active' ? 'border-forest bg-white shadow-sm' : 'border-[#ece7dc] bg-white/70' }} {{ $state === 'upcoming' ? 'opacity-70' : '' }}">
            @if ($href)
                <a href="{{ $href }}" class="flex items-center gap-3">
            @else
                <div class="flex items-center gap-3" @if($state === 'upcoming') aria-disabled="true" @endif>
            @endif
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sm {{ $state === 'active' ? 'bg-forest text-ivory' : ($state === 'done' ? 'bg-[#e7f0e4] text-forest' : 'bg-sand text-mist') }}">{{ $number }}</span>
                <span class="min-w-0">
                    <span class="block text-[11px] uppercase tracking-[0.14em] text-mist">
                        {{ $state === 'done' ? 'Complete' : ($state === 'upcoming' ? 'Upcoming' : 'Step '.$number) }}
                    </span>
                    <span class="block truncate text-sm font-medium text-charcoal">{{ $label }}</span>
                </span>
            @if ($href)
                </a>
            @else
                </div>
            @endif
        </li>
    @endforeach
</ol>
