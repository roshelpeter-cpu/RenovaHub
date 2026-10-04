<a href="{{ route('homeowner.projects.show', $project) }}" class="flex min-w-[12rem] items-center gap-3">
    @if ($project->coverUrl())
        <img src="{{ $project->coverUrl() }}" alt="" class="h-10 w-10 shrink-0 rounded-lg object-cover">
    @else
        <span class="h-10 w-10 shrink-0 rounded-lg bg-[#EFE8DA]"></span>
    @endif
    <span class="min-w-0">
        <span class="block truncate font-medium text-[#123D2B]">{{ $project->name }}</span>
        <span class="block truncate text-xs text-[#66756C]">{{ $project->city ?: $project->cataloguePlaceLabel() }}</span>
    </span>
</a>
