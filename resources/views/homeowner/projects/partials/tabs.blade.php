@php
    $section = $section ?? 'overview';
    $tabs = [
        ['Overview', route('homeowner.projects.show', $project), $section === 'overview' && request()->routeIs('homeowner.projects.show')],
        ['Design Process', route('homeowner.projects.show', [$project, 'tab' => 'design-process']), $section === 'design-process'],
        ['Tasks', route('homeowner.projects.tasks', $project), request()->routeIs('homeowner.projects.tasks*')],
        ['Documents', route('homeowner.projects.documents', $project), request()->routeIs('homeowner.projects.documents*')],
        ['Mood Board', route('homeowner.projects.mood-board', $project), request()->routeIs('homeowner.projects.mood-board*')],
        ['Quotations', route('homeowner.projects.quotations', $project), request()->routeIs('homeowner.quotations*', 'homeowner.projects.quotations*')],
        ['Change Requests', route('homeowner.projects.change-requests', $project), request()->routeIs('homeowner.change-requests*', 'homeowner.projects.change-requests*')],
        ['Messages', route('homeowner.projects.messages', $project), request()->routeIs('homeowner.projects.messages*')],
    ];
@endphp
<nav class="mt-6 flex gap-6 overflow-x-auto border-b border-[#ece7dc] text-sm" aria-label="Project sections">
    @foreach ($tabs as [$label, $href, $active])
        <a href="{{ $href }}" class="shrink-0 pb-3 {{ $active ? 'border-b-2 border-[#123D2B] font-medium text-[#123D2B]' : 'text-[#66756C] hover:text-[#123D2B]' }}" @if($active) aria-current="page" @endif>{{ $label }}</a>
    @endforeach
</nav>
