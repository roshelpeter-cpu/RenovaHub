<x-homeowner-layout :title="$project->name" :flush="true">
    @php
        $designer = $project->designer;
        $contractor = $project->contractor;
        $gallery = $project->gallery();
        $hero = $gallery->first()?->url() ?: $project->coverUrl();
        $meta = $project->workspace_meta ?? [];
        $section = $section ?? 'overview';
    @endphp

    <div class="mx-auto max-w-[90rem] px-4 py-6 sm:px-6 lg:px-8">
        <p class="text-sm text-[#66756C]">
            <a href="{{ route('homeowner.home') }}" class="hover:text-[#123D2B]">Home</a>
            <span class="mx-1 text-[#c9c2b4]">›</span>
            <a href="{{ route('homeowner.projects.index') }}" class="hover:text-[#123D2B]">My Projects</a>
            <span class="mx-1 text-[#c9c2b4]">›</span>
            {{ $project->name }}
        </p>

        <div class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1.55fr)_20rem]">
            <div class="relative overflow-hidden rounded-[1.5rem]">
                @if ($hero)
                    <img src="{{ $hero }}" alt="" class="h-[22rem] w-full object-cover">
                @endif
                <span class="absolute left-4 top-4 rounded-full bg-[#E7F0E4] px-3 py-1 text-xs font-medium text-[#123D2B]">{{ $project->status === 'completed' ? 'Completed' : 'In Progress' }}</span>
            </div>

            <aside class="space-y-4">
                <section class="rounded-[1.4rem] border border-[#ece7dc] bg-white p-5">
                    <div class="flex items-center justify-between">
                        <h2 class="font-serif text-xl text-[#123D2B]">Project Team</h2>
                        <a href="{{ route('homeowner.projects.team', $project) }}" class="text-sm font-medium text-[#123D2B]">View All →</a>
                    </div>
                    @foreach (['designer' => 'Designer', 'contractor' => 'Contractor'] as $role => $roleLabel)
                        @php $member = $project->{$role}; @endphp
                        <div class="mt-4 flex items-center gap-3">
                            <img src="{{ $member?->professionalProfile?->listingImageUrl() ?: $member?->profile_photo_url }}" alt="" class="h-12 w-12 rounded-full object-cover">
                            <div class="min-w-0 flex-1">
                                <p class="text-xs text-[#66756C]">{{ $roleLabel }}</p>
                                <p class="font-medium text-[#123D2B]">{{ $member?->professionalProfile?->displayName() ?? $member?->name ?? 'Not selected' }}</p>
                                <p class="text-xs text-[#66756C]">{{ $role === 'designer' ? ($member?->professionalProfile?->title ?? 'Designer') : ($member?->isContractor() ? ($member?->professionalProfile?->title ?? 'Contractor') : 'Contractor') }}</p>
                            </div>
                            @if ($member)
                                <form method="POST" action="{{ route('homeowner.professionals.contact', $member) }}">
                                    @csrf
                                    <button type="submit" class="flex h-9 w-9 items-center justify-center rounded-full text-[#123D2B]" aria-label="Message {{ $roleLabel }}">
                                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 6h16v10H8l-4 4V6Z"/></svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </section>

                <section class="rounded-[1.4rem] bg-[#123D2B] p-5 text-[#F6F1E7]">
                    <p class="text-sm">Project Budget</p>
                    <p class="mt-1 font-serif text-3xl">LKR {{ number_format((float) $project->estimated_budget, 0) }}</p>
                </section>

                <section class="rounded-[1.4rem] border border-[#ece7dc] bg-white p-5">
                    <div class="flex items-end justify-between">
                        <h2 class="font-medium text-[#123D2B]">Overall Progress</h2>
                        <p class="font-serif text-2xl text-[#123D2B]">{{ $project->progress }}%</p>
                    </div>
                    <div class="mt-2 h-2 overflow-hidden rounded-full bg-[#EFE8DA]">
                        <div class="h-full rounded-full bg-[#123D2B]" style="width: {{ $project->progress }}%"></div>
                    </div>
                    <ul class="mt-4 space-y-2 text-sm">
                        @foreach ($project->workspaceStages() as $stage)
                            <li class="flex items-center justify-between">
                                <span class="{{ $stage->percent >= 100 ? 'text-[#2F6B49]' : 'text-[#123D2B]' }}">{{ $stage->percent >= 100 ? '✓' : '○' }} {{ $stage->label }}</span>
                                <span class="text-xs text-[#66756C]">{{ $stage->status }}</span>
                            </li>
                        @endforeach
                    </ul>
                </section>
            </aside>
        </div>

        <h1 class="mt-8 font-serif text-4xl text-[#123D2B]">{{ $project->name }}</h1>
        <p class="mt-2 text-sm text-[#66756C]">
            {{ $project->cataloguePlaceLabel() }}
            · {{ $project->propertyTypeLabel() }}
            · {{ $project->displayTypeLabel() }}
            @if ($project->expected_start_date) · Started {{ $project->expected_start_date->format('M Y') }} @endif
            @if ($project->expected_completion_date) · Expected {{ $project->expected_completion_date->format('M Y') }} @endif
        </p>
        <p class="mt-4 max-w-4xl text-sm leading-relaxed text-[#66756C]">{{ $project->description }}</p>

        @include('homeowner.projects.partials.tabs', ['project' => $project, 'section' => $section])

        @if ($section === 'design-process')
            <section class="mt-8 grid gap-6 lg:grid-cols-[16rem_minmax(0,1fr)]">
                <ol class="space-y-3">
                    @foreach ($project->workspaceStages() as $stage)
                        <li class="rounded-2xl border px-4 py-3 {{ $stage->percent > 0 && $stage->percent < 100 ? 'border-[#123D2B] bg-[#F3F7F1]' : 'border-[#ece7dc]' }}">
                            <p class="font-medium text-[#123D2B]">{{ $stage->label }}</p>
                            <p class="text-xs text-[#66756C]">{{ $meta['stages'][$stage->key]['dates'] ?? $stage->status }}</p>
                        </li>
                    @endforeach
                </ol>
                <div class="rounded-[1.4rem] border border-[#ece7dc] p-6">
                    <p class="text-sm text-[#66756C]">Current Stage: <span class="font-medium text-[#123D2B]">{{ collect($project->workspaceStages())->first(fn ($stage) => $stage->percent > 0 && $stage->percent < 100)?->label ?? 'Design' }}</span></p>
                    <p class="mt-2 font-serif text-4xl text-[#123D2B]">{{ $project->progress }}%</p>
                    <h2 class="mt-6 font-serif text-2xl text-[#123D2B]">Stage Details</h2>
                    <p class="mt-2 text-sm leading-relaxed text-[#66756C]">{{ $meta['stage_details'] ?? $project->requirements }}</p>
                    @if (! empty($meta['activities']))
                        <h3 class="mt-6 font-medium text-[#123D2B]">Key Activities</h3>
                        <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-[#66756C]">
                            @foreach ($meta['activities'] as $activity)
                                <li>{{ $activity }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </section>
        @else
            <div class="mt-8 grid gap-8 lg:grid-cols-[minmax(0,1.15fr)_minmax(18rem,0.85fr)]">
                <section>
                    <h2 class="font-serif text-2xl text-[#123D2B]">Project Overview</h2>
                    <p class="mt-3 whitespace-pre-line text-sm leading-relaxed text-[#66756C]">{{ $meta['overview'] ?? $project->requirements ?? $project->description }}</p>
                </section>
                <section class="rounded-[1.4rem] border border-[#ece7dc] p-5">
                    <h2 class="font-serif text-xl text-[#123D2B]">Key Details</h2>
                    <dl class="mt-4 grid grid-cols-2 gap-4 text-sm">
                        <div><dt class="text-xs text-[#66756C]">Client</dt><dd class="font-medium text-[#123D2B]">{{ auth()->user()->name }}</dd></div>
                        <div><dt class="text-xs text-[#66756C]">Project Type</dt><dd class="font-medium text-[#123D2B]">{{ $project->displayTypeLabel() }}</dd></div>
                        <div><dt class="text-xs text-[#66756C]">Current Stage</dt><dd class="font-medium text-[#123D2B]">{{ collect($project->workspaceStages())->first(fn ($stage) => $stage->percent > 0 && $stage->percent < 100)?->label ?? ($project->status === 'completed' ? 'Completed' : 'Planning') }}</dd></div>
                        <div><dt class="text-xs text-[#66756C]">Property Size</dt><dd class="font-medium text-[#123D2B]">{{ $project->size_sq_ft ? number_format($project->size_sq_ft).' sq. ft' : '—' }}</dd></div>
                        <div><dt class="text-xs text-[#66756C]">Start Date</dt><dd class="font-medium text-[#123D2B]">{{ $project->expected_start_date?->format('M Y') ?? '—' }}</dd></div>
                        <div><dt class="text-xs text-[#66756C]">Expected Completion</dt><dd class="font-medium text-[#123D2B]">{{ $project->expected_completion_date?->format('M Y') ?? '—' }}</dd></div>
                    </dl>
                </section>
            </div>

            <section class="mt-10">
                <div class="flex items-end justify-between">
                    <h2 class="font-serif text-2xl text-[#123D2B]">Project Gallery</h2>
                    <span class="text-sm text-[#66756C]">{{ max($gallery->count(), $hero ? 1 : 0) }} photos</span>
                </div>
                <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    @forelse ($gallery as $index => $image)
                        <a href="{{ $image->url() }}" target="_blank" rel="noopener" class="{{ $index === 0 ? 'sm:col-span-2 xl:col-span-2 xl:row-span-2' : '' }}">
                            <img src="{{ $image->url() }}" alt="{{ $image->original_name }}" class="h-full min-h-[9rem] w-full rounded-2xl object-cover {{ $index === 0 ? 'xl:min-h-[22rem]' : '' }}">
                        </a>
                    @empty
                        @if ($hero)
                            <img src="{{ $hero }}" alt="" class="h-64 w-full rounded-2xl object-cover sm:col-span-2">
                        @endif
                    @endforelse
                </div>
            </section>
        @endif
    </div>
</x-homeowner-layout>
