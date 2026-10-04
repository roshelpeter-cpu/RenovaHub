@php
    $cards = $completed
        ? [
            ['Final Project Cost', 'LKR '.number_format($project->finalCostAmount(), 0), 'Actual completion cost', 'wallet'],
            ['Project Duration', $project->durationLabel() ?? '—', $project->durationRangeLabel() ?? 'Dates not set', 'calendar'],
            ['Overall Progress', $project->progress.'%', $project->progressCaption(), 'check'],
            ['Project Status', 'Completed', $project->metaString('status_note', 'Handover finished'), 'star'],
        ]
        : [
            ['Project Budget', 'LKR '.number_format($project->budgetTotalAmount(), 0), 'Approved budget', 'wallet'],
            ['Project Duration', $project->durationLabel() ?? '—', $project->durationRangeLabel() ?? 'Dates not set', 'calendar'],
            ['Overall Progress', $project->progress.'%', $project->progressCaption(), 'check'],
            ['Current Stage', $project->currentStageLabel(), 'In Progress', 'star'],
        ];
@endphp

@foreach ($cards as [$label, $value, $note, $icon])
    <article class="rounded-2xl border border-[#ece7dc] bg-white p-3 shadow-sm">
        <div class="flex items-start justify-between gap-2">
            <p class="text-[11px] text-[#66756C]">{{ $label }}</p>
            <span class="text-[#2F6B49]">
                @if ($icon === 'wallet')
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="3" y="6" width="18" height="13" rx="2"/><path d="M16 12h2"/></svg>
                @elseif ($icon === 'calendar')
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="4" y="5" width="16" height="15" rx="2"/><path d="M8 3v4M16 3v4M4 10h16"/></svg>
                @elseif ($icon === 'check')
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="8"/><path d="m8.5 12.2 2.4 2.4 4.6-5"/></svg>
                @else
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6"><path d="m12 3 2.2 6.2H21l-5.2 3.8 2 6.3L12 16.8 6.2 19.3l2-6.3L3 9.2h6.8L12 3Z"/></svg>
                @endif
            </span>
        </div>
        <p class="mt-2 font-serif text-lg leading-tight text-[#123D2B]">{{ $value }}</p>
        <p class="mt-1 text-[11px] text-[#66756C]">{{ $note }}</p>
    </article>
@endforeach
