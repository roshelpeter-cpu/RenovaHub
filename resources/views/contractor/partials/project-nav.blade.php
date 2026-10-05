@php
    $sections = [
        'overview' => 'Overview',
        'quotations' => 'Quotations',
        'budget' => 'Budget',
        'suppliers' => 'Suppliers',
        'tasks' => 'Tasks',
        'documents' => 'Documents',
        'change-requests' => 'Change Requests',
        'messages' => 'Messages',
    ];
@endphp
<nav class="mt-5 flex gap-2 overflow-x-auto border-b border-[#ece7dc]" aria-label="Project">
    @foreach ($sections as $key => $label)
        @php
            $href = $key === 'overview'
                ? route('contractor.projects.show', $project)
                : ($key === 'messages' ? route('contractor.projects.messages', $project) : route('contractor.projects.section', [$project, $key]));
            $active = ($section ?? 'overview') === $key;
        @endphp
        <a href="{{ $href }}" class="shrink-0 px-3 py-3 text-sm {{ $active ? 'border-b-2 border-[#1F7A4D] font-medium text-[#123D2B]' : 'text-[#66756C]' }}">{{ $label }}</a>
    @endforeach
</nav>
