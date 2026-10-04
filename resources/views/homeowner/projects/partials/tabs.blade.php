@php
    $tabs = [
        'homeowner.projects.show' => 'Overview',
        'homeowner.projects.tasks' => 'Tasks',
        'homeowner.projects.documents' => 'Documents',
        'homeowner.projects.mood-board' => 'Mood Board',
        'homeowner.projects.quotations' => 'Quotations',
        'homeowner.projects.change-requests' => 'Change Requests',
        'homeowner.projects.messages' => 'Messages',
        'homeowner.projects.payments' => 'Payments',
        'homeowner.projects.activity' => 'Activity',
    ];
@endphp
<nav class="mt-6 flex gap-2 overflow-x-auto pb-1" aria-label="Project sections">
    @foreach ($tabs as $routeName => $label)
        <a href="{{ route($routeName, $project) }}" class="shrink-0 rounded-full px-4 py-2 text-sm transition duration-300 {{ request()->routeIs($routeName) ? 'bg-forest text-ivory' : 'bg-white text-charcoal hover:text-forest' }}" @if(request()->routeIs($routeName)) aria-current="page" @endif>{{ $label }}</a>
    @endforeach
</nav>
