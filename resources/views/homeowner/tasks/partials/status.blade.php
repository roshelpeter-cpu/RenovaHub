@php
    $tone = match ($task->displayStatus()) {
        'completed' => 'bg-[#E7F0E4] text-[#123D2B]',
        'in_progress' => 'bg-[#F3E6D4] text-[#8A5A12]',
        'overdue' => 'bg-[#F8E6E3] text-[#8C3A32]',
        default => 'bg-[#F6F1E7] text-[#66756C]',
    };
@endphp
<span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $tone }}">{{ $task->statusLabel() }}</span>
