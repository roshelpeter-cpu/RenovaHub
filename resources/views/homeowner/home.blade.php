<x-homeowner-layout title="Home" :flush="true">
    <div class="relative overflow-hidden bg-white">
        <svg class="pointer-events-none absolute left-0 top-6 hidden h-[22rem] w-40 text-[#DCE7D8] lg:block" viewBox="0 0 140 320" fill="currentColor" aria-hidden="true">
            <ellipse cx="18" cy="50" rx="42" ry="70" opacity="0.75"/>
            <ellipse cx="8" cy="145" rx="32" ry="55" opacity="0.5"/>
            <ellipse cx="28" cy="230" rx="26" ry="42" opacity="0.35"/>
        </svg>
        <svg class="pointer-events-none absolute bottom-0 right-0 hidden h-64 w-32 text-[#DCE7D8] lg:block" viewBox="0 0 120 260" fill="currentColor" aria-hidden="true">
            <ellipse cx="110" cy="80" rx="40" ry="70" opacity="0.45"/>
            <ellipse cx="100" cy="180" rx="30" ry="55" opacity="0.3"/>
        </svg>

        <section class="relative mx-auto grid max-w-[90rem] items-center gap-6 px-4 pb-6 pt-8 sm:px-6 lg:grid-cols-[minmax(0,0.88fr)_minmax(0,1.12fr)] lg:px-8 lg:pb-4 lg:pt-6">
            <div>
                <p class="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.22em] text-[#2F6B49]">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor"><path d="M12 3c.4 4 3 7 7 8-4 .4-7 3-8 7-.4-4-3-7-7-8 4-.4 7-3 8-7Z"/></svg>
                    {{ $greeting }}, {{ $firstName }}
                </p>
                <h1 class="mt-4 max-w-xl font-serif text-[2.6rem] font-medium leading-[1.08] tracking-[-0.03em] text-[#123D2B] sm:text-5xl">Bring your renovation vision to life.</h1>
                <p class="mt-4 max-w-md text-sm leading-relaxed text-[#66756C] sm:text-[15px]">Keep track of your projects, collaborate with your design and construction team, and stay updated on every stage of your renovation.</p>
                <div class="mt-7 flex flex-wrap gap-3">
                    <a href="{{ route('homeowner.projects.create') }}" class="inline-flex items-center rounded-full bg-[#123D2B] px-5 py-2.5 text-sm font-medium text-white">+ Create New Project</a>
                    <a href="{{ route('homeowner.projects.index') }}" class="inline-flex items-center rounded-full border border-[#123D2B] px-5 py-2.5 text-sm font-medium text-[#123D2B]">View My Projects →</a>
                </div>
            </div>
            <div class="relative min-h-[17rem] lg:min-h-[23rem]">
                <div class="h-full overflow-hidden rounded-[2rem]" style="mask-image: linear-gradient(90deg, transparent 0%, #000 10%, #000 100%);">
                    <img src="{{ asset('images/renova/about-interior.jpg') }}" alt="" class="h-full min-h-[17rem] w-full object-cover lg:min-h-[23rem]">
                </div>
                <p class="pointer-events-none absolute right-4 top-8 hidden max-w-[8.5rem] text-right font-serif text-[1.65rem] leading-[1.15] text-[#123D2B]/80 lg:block" style="font-family: Caveat, Fraunces, serif; transform: rotate(-6deg);">Spaces<br>for a better<br>tomorrow</p>
            </div>
        </section>
    </div>

    <div class="mx-auto max-w-[90rem] px-4 pb-14 sm:px-6 lg:px-8">
        <div class="grid items-end gap-5 lg:grid-cols-[minmax(0,1.2fr)_minmax(17rem,0.8fr)]">
            <section>
                <div class="flex items-end justify-between gap-3">
                    <h2 class="font-serif text-[1.7rem] text-[#123D2B]">Your Projects</h2>
                    <a href="{{ route('homeowner.projects.index') }}" class="text-sm font-medium text-[#123D2B]">View All Projects →</a>
                </div>
                <dl class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
                    @foreach ([
                        ['Total Projects', $summary['total'], 'M4 10.5 12 4l8 6.5V20a1 1 0 0 1-1 1h-5v-6H10v6H5a1 1 0 0 1-1-1v-9.5Z'],
                        ['In Progress', $summary['in_progress'], 'M12 7v5l3 2M12 21a9 9 0 1 1 0-18 9 9 0 0 1 0 18Z'],
                        ['Completed', $summary['completed'], 'M20 6 9 17l-5-5'],
                        ['Total Project Value', \App\Models\Project::compactMoney($summary['value']), 'M4 7h16v11H4Z'],
                    ] as [$label, $value, $icon])
                        <div class="rounded-2xl border border-[#ece7dc] bg-white px-3 py-4 text-center shadow-[0_10px_24px_-18px_rgba(18,61,43,0.45)]">
                            <span class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-[#E7F0E4] text-[#123D2B]">
                                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="{{ $icon }}"/></svg>
                            </span>
                            <dd class="font-serif text-2xl text-[#123D2B]">{{ $value }}</dd>
                            <dt class="mt-1 text-xs text-[#66756C]">{{ $label }}</dt>
                        </div>
                    @endforeach
                </dl>
            </section>

            <article class="overflow-hidden rounded-[1.5rem] bg-[#123D2B] text-white">
                <div class="grid h-full min-h-[9.5rem] sm:grid-cols-[minmax(0,1.15fr)_minmax(7rem,0.85fr)]">
                    <div class="p-5">
                        <p class="text-sm text-white/80">Need a professional?</p>
                        <h2 class="mt-1 font-serif text-[1.45rem] leading-tight">Find trusted designers and contractors</h2>
                        <p class="mt-2 text-sm leading-relaxed text-white/75">Work with experienced professionals to bring your vision to life.</p>
                        <a href="{{ route('homeowner.explore') }}" class="mt-4 inline-flex rounded-full bg-white px-4 py-2 text-sm font-medium text-[#123D2B]">Browse Professionals →</a>
                    </div>
                    <img src="{{ asset('images/renova/feature-collab.jpg') }}" alt="" class="hidden h-full w-full object-cover sm:block">
                </div>
            </article>
        </div>

        <div class="mt-10 grid gap-6 xl:grid-cols-[minmax(0,1.28fr)_minmax(20rem,0.72fr)]">
            <section>
                <div class="flex items-end justify-between gap-3">
                    <h2 class="font-serif text-[1.7rem] text-[#123D2B]">Your Active Project</h2>
                    @if ($activeProject)
                        <a href="{{ route('homeowner.projects.show', $activeProject) }}" class="text-sm font-medium text-[#123D2B]">View Project →</a>
                    @endif
                </div>

                @if ($activeProject)
                    <article class="mt-4 overflow-hidden rounded-[1.5rem] border border-[#ece7dc] bg-white p-4 shadow-[0_12px_32px_-24px_rgba(18,61,43,0.45)] sm:p-5">
                        <div class="grid gap-5 lg:grid-cols-[minmax(0,1.4fr)_15.5rem]">
                            <div class="relative overflow-hidden rounded-2xl">
                                <img src="{{ $activeProject->coverUrl() ?: asset('images/renova/about-exterior.jpg') }}" alt="" class="h-52 w-full object-cover sm:h-60">
                                <span class="absolute right-3 top-3 inline-flex items-center gap-1 rounded-full bg-[#e7f0e4] px-3 py-1 text-xs font-medium text-[#123D2B]">{{ $activeProject->statusLabel() }}</span>
                            </div>
                            <div class="rounded-2xl border border-[#ece7dc] bg-[#FBF9F4] p-4">
                                <h3 class="text-sm font-medium text-[#123D2B]">Project Team</h3>
                                @foreach (['Designer' => $activeProject->designer, 'Contractor' => $activeProject->contractor] as $role => $member)
                                    <div class="mt-3 flex items-center gap-3">
                                        <img src="{{ $member?->professionalProfile?->avatarUrl() ?: $member?->profile_photo_url }}" alt="" class="h-10 w-10 rounded-full object-cover">
                                        <div>
                                            <p class="text-xs text-[#66756C]">{{ $role }}</p>
                                            <p class="text-sm font-medium text-[#18352A]">{{ $member?->professionalProfile?->displayName() ?? $member?->name ?? 'Not selected' }}</p>
                                        </div>
                                    </div>
                                @endforeach
                                <div class="mt-4 space-y-3 text-sm">
                                    <p class="flex gap-2 text-[#66756C]">
                                        <svg viewBox="0 0 24 24" class="mt-0.5 h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="4" y="5" width="16" height="15" rx="2"/></svg>
                                        <span>Expected Completion<br><span class="font-medium text-[#18352A]">{{ $activeProject->expected_completion_date?->format('M Y') ?? 'Not set' }}</span></span>
                                    </p>
                                    <p class="flex gap-2 text-[#66756C]">
                                        <svg viewBox="0 0 24 24" class="mt-0.5 h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="3.5" y="6.5" width="17" height="12" rx="2"/></svg>
                                        <span>Budget<br><span class="font-medium text-[#18352A]">{{ $activeProject->estimated_budget !== null ? 'LKR '.number_format((float) $activeProject->estimated_budget, 0) : 'Not set' }}</span></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <h3 class="mt-5 font-serif text-2xl text-[#123D2B]">{{ $activeProject->name }}</h3>
                        <p class="mt-1 text-sm text-[#66756C]">{{ $activeProject->city ? $activeProject->city.', Sri Lanka' : ($activeProject->locationLabel() ?: 'Location not added') }}</p>
                        <p class="mt-3 max-w-3xl text-sm leading-relaxed text-[#66756C]">{{ $activeProject->description }}</p>
                        <div class="mt-5">
                            <div class="mb-2 flex items-center justify-between text-sm">
                                <span class="text-[#66756C]">Overall Progress</span>
                                <span class="font-medium text-[#123D2B]">{{ $activeProject->progress ?? 0 }}%</span>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full bg-[#EFE8DA]">
                                <div class="h-full rounded-full bg-[#123D2B]" style="width: {{ $activeProject->progress ?? 0 }}%"></div>
                            </div>
                        </div>
                        <ol class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-5">
                            @foreach ($activeProject->orderedProgress() as $stage)
                                <li class="text-center">
                                    <span class="mx-auto flex h-9 w-9 items-center justify-center rounded-full {{ $stage->percent >= 100 ? 'bg-[#E7F0E4] text-[#123D2B]' : ($stage->percent > 0 ? 'border-2 border-[#123D2B] text-[#123D2B]' : 'border border-[#d9d1c3] text-[#66756C]') }}">
                                        @if ($stage->percent >= 100)
                                            <svg viewBox="0 0 16 16" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m3.5 8 3 3 6-6"/></svg>
                                        @else
                                            <span class="text-[10px] font-medium">{{ $stage->percent > 0 ? $stage->percent.'%' : '' }}</span>
                                        @endif
                                    </span>
                                    <p class="mt-2 text-sm font-medium text-[#123D2B]">{{ $stage->label() }}</p>
                                    <p class="text-xs text-[#66756C]">{{ $stage->homeStatus() }}</p>
                                </li>
                            @endforeach
                        </ol>
                    </article>
                @else
                    <div class="mt-5">@include('homeowner.partials.empty', ['title' => 'No projects yet', 'body' => 'Create a project to see it here.', 'action' => ['url' => route('homeowner.projects.create'), 'label' => 'Create New Project']])</div>
                @endif
            </section>

            <div class="space-y-5">
                <section class="rounded-[1.5rem] border border-[#ece7dc] bg-white p-5">
                    <div class="flex items-center justify-between">
                        <h2 class="font-serif text-xl text-[#123D2B]">Recent Activity</h2>
                        @if ($activeProject)
                            <a href="{{ route('homeowner.projects.activity', $activeProject) }}" class="text-sm font-medium text-[#123D2B]">View All →</a>
                        @endif
                    </div>
                    @forelse ($activityGroups as $label => $items)
                        <h3 class="mt-4 text-[11px] font-semibold uppercase tracking-[0.16em] text-[#66756C]">{{ $label }}</h3>
                        <ul class="mt-2 space-y-3">
                            @foreach ($items as $entry)
                                <li class="flex gap-3 text-sm">
                                    <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#E7F0E4] text-[#123D2B]">
                                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 7h16v12H4Z"/></svg>
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-[#18352A]">{{ $entry->description }}</p>
                                        @if (! empty($entry->properties['detail']))
                                            <p class="text-xs text-[#66756C]">{{ $entry->properties['detail'] }}</p>
                                        @endif
                                    </div>
                                    <span class="shrink-0 text-xs text-[#66756C]">{{ $entry->created_at->format('h:i A') }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @empty
                        <p class="mt-3 text-sm text-[#66756C]">No activity has been recorded yet.</p>
                    @endforelse
                </section>

                <section class="rounded-[1.5rem] border border-[#ece7dc] bg-white p-5">
                    <div class="flex items-center justify-between">
                        <h2 class="font-serif text-xl text-[#123D2B]">Action Required</h2>
                        <a href="{{ route('homeowner.projects.index') }}" class="text-sm font-medium text-[#123D2B]">View All →</a>
                    </div>
                    <ul class="mt-3 divide-y divide-[#ece7dc]">
                        @forelse ($actions as $action)
                            <li>
                                <a href="{{ $action['url'] }}" class="flex items-start gap-3 py-3">
                                    <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg {{ ($action['icon'] ?? '') === 'alert' ? 'bg-[#C4503A] text-white' : 'bg-[#E7F0E4] text-[#123D2B]' }}">
                                        @if (($action['icon'] ?? '') === 'alert')
                                            !
                                        @else
                                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M7 4h10v16H7Z"/></svg>
                                        @endif
                                    </span>
                                    <span class="min-w-0 flex-1">
                                        <span class="block text-sm font-medium text-[#123D2B]">{{ $action['title'] }}</span>
                                        <span class="block text-xs text-[#66756C]">{{ $action['body'] }}</span>
                                        @if ($action['amount'])
                                            <span class="mt-0.5 block text-sm text-[#18352A]">{{ $action['amount'] }}</span>
                                        @endif
                                    </span>
                                    <span class="text-[#66756C]">›</span>
                                </a>
                            </li>
                        @empty
                            <li class="py-3 text-sm text-[#66756C]">Nothing needs your attention.</li>
                        @endforelse
                    </ul>
                </section>
            </div>
        </div>

        <section class="mt-10">
            <h2 class="font-serif text-[1.7rem] text-[#123D2B]">Manage Your Project</h2>
            <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @php
                    $pid = $activeProject;
                    $shortcuts = [
                        ['Tasks', 'View project tasks and progress', $pid ? route('homeowner.projects.tasks', $pid) : route('homeowner.tasks.index'), 'M8 6h11M8 12h11M8 18h11'],
                        ['Documents', 'Access and manage project documents', $pid ? route('homeowner.projects.documents', $pid) : route('homeowner.documents.index'), 'M7 3.5h7l4 4V20.5H7Z'],
                        ['Mood Board', 'View designs and inspiration', $pid ? route('homeowner.projects.mood-board', $pid) : route('homeowner.mood-board.index'), 'M4 5h16v14H4Z'],
                        ['Quotations', 'Review quotes and approve', $pid ? route('homeowner.projects.quotations', $pid) : route('homeowner.quotations.index'), 'M7 3.5h8l4 4V20.5H7Z'],
                        ['Change Requests', 'Track and manage project changes', $pid ? route('homeowner.projects.change-requests', $pid) : route('homeowner.change-requests.index'), 'M4 20h4l11-11-4-4L4 16v4Z'],
                        ['Messages', 'Communicate with your project team', route('homeowner.messages.index'), 'M4 6h16v10H8l-4 4V6Z'],
                    ];
                @endphp
                @foreach ($shortcuts as [$title, $body, $url, $icon])
                    <a href="{{ $url }}" class="flex items-start gap-3 rounded-[1.35rem] border border-[#ece7dc] bg-white p-4">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-[#E7F0E4] text-[#123D2B]">
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="{{ $icon }}"/></svg>
                        </span>
                        <span>
                            <span class="block font-medium text-[#123D2B]">{{ $title }}</span>
                            <span class="mt-1 block text-sm text-[#66756C]">{{ $body }}</span>
                        </span>
                        <span class="ml-auto text-[#66756C]">›</span>
                    </a>
                @endforeach
            </div>
        </section>
    </div>
</x-homeowner-layout>
