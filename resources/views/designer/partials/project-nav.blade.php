@php
    $tabs = [
        ['Overview', route('designer.projects.show', $project), request()->routeIs('designer.projects.show')],
        ['Design Brief', route('designer.projects.brief', $project), request()->routeIs('designer.projects.brief')],
        ['Mood Board', route('designer.projects.mood-board', $project), request()->routeIs('designer.projects.mood-board')],
        ['Design Concepts', route('designer.concepts.index', ['project' => $project->id]), request()->routeIs('designer.concepts.*') && (int) request()->route('project')?->id === $project->id],
        ['Revisions', route('designer.projects.revisions', $project), request()->routeIs('designer.projects.revisions')],
        ['Documents', route('designer.projects.documents', $project), request()->routeIs('designer.projects.documents')],
        ['Tasks', route('designer.projects.tasks', $project), request()->routeIs('designer.projects.tasks')],
        ['Messages', route('designer.projects.messages', $project), request()->routeIs('designer.projects.messages')],
    ];
@endphp
<nav class="flex gap-1 overflow-x-auto border-b border-[#ece7dc]" aria-label="Project sections">
    @foreach ($tabs as [$label, $href, $active])
        <a href="{{ $href }}" class="shrink-0 px-3 py-3 text-sm {{ $active ? 'border-b-2 border-[#123D2B] font-medium text-[#123D2B]' : 'text-[#66756C]' }}">{{ $label }}</a>
    @endforeach
</nav>
