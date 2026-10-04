@php
    $section = $section ?? 'overview';
    $project->ensureSectionCounts();
    $count = fn (string $relation, int $value) => $value > 0 ? $relation.' ('.$value.')' : $relation;
    $tabs = [
        ['Overview', route('homeowner.projects.show', $project), $section === 'overview' && request()->routeIs('homeowner.projects.show')],
        [$count('Tasks', (int) $project->tasks_count), route('homeowner.projects.tasks', $project), request()->routeIs('homeowner.projects.tasks*')],
        [$count('Documents', (int) $project->documents_count), route('homeowner.projects.documents', $project), request()->routeIs('homeowner.projects.documents*')],
        ['Mood Board', route('homeowner.projects.mood-board', $project), request()->routeIs('homeowner.projects.mood-board*')],
        [$count('Quotations', (int) $project->quotations_count), route('homeowner.projects.quotations', $project), request()->routeIs('homeowner.quotations.show', 'homeowner.projects.quotations')],
        [$count('Change Requests', (int) $project->change_requests_count), route('homeowner.projects.change-requests', $project), request()->routeIs('homeowner.change-requests.show', 'homeowner.change-requests.create', 'homeowner.projects.change-requests*')],
    ];
    if ($project->isCompleted()) {
        $tabs[] = ['Feedback', route('homeowner.projects.feedback', $project), request()->routeIs('homeowner.projects.feedback')];
    }
@endphp
<nav class="mt-8 flex gap-6 overflow-x-auto border-b border-[#ece7dc] text-sm" aria-label="Project sections">
    @foreach ($tabs as [$label, $href, $active])
        <a href="{{ $href }}" class="shrink-0 pb-3 {{ $active ? 'border-b-2 border-[#123D2B] font-medium text-[#123D2B]' : 'text-[#66756C] hover:text-[#123D2B]' }}" @if($active) aria-current="page" @endif>{{ $label }}</a>
    @endforeach
</nav>
