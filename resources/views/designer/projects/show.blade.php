<x-designer-layout title="{{ $project->name }}">
    @include('designer.partials.project-header')
    <div class="mt-6 grid gap-4 lg:grid-cols-3">
        <article class="rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm lg:col-span-2">
            <h2 class="font-serif text-2xl text-[#123D2B]">Project overview</h2>
            <p class="mt-3 text-sm leading-relaxed text-[#66756C]">{{ $project->description }}</p>
            <p class="mt-4 text-sm text-[#123D2B]">Current stage: {{ ($project->workspace_meta ?? [])['design_stage'] ?? 'Design' }}</p>
            <ol class="mt-4 space-y-2">
                @foreach ($stages as $stage)
                    <li class="flex items-center justify-between rounded-xl bg-[#F7F4EE] px-3 py-2 text-sm">
                        <span class="text-[#123D2B]">{{ $stage['label'] }}</span>
                        <span class="text-xs text-[#66756C]">{{ $stage['status'] }}</span>
                    </li>
                @endforeach
            </ol>
        </article>
        <div class="space-y-4">
            <article class="rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm">
                <h2 class="font-serif text-xl text-[#123D2B]">Project team</h2>
                <ul class="mt-3 space-y-2 text-sm">
                    <li class="text-[#123D2B]">Homeowner · {{ $project->homeowner?->name }}</li>
                    <li class="text-[#123D2B]">Designer · {{ auth()->user()->name }}</li>
                    <li class="text-[#123D2B]">Contractor · {{ $project->contractor?->name ?? 'Not assigned' }}</li>
                </ul>
                <a href="{{ route('designer.projects.messages', $project) }}" class="mt-4 inline-flex text-sm font-medium text-[#123D2B] hover:underline">Message the team</a>
            </article>
            <article class="rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm">
                <h2 class="font-serif text-xl text-[#123D2B]">Recent design activity</h2>
                <ul class="mt-3 space-y-2">
                    @forelse ($activity as $item)
                        <li class="text-sm text-[#66756C]">{{ $item->description }}</li>
                    @empty
                        <li class="text-sm text-[#66756C]">No activity yet.</li>
                    @endforelse
                </ul>
            </article>
            @if ($feedback->isNotEmpty())
                <article class="rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm">
                    <h2 class="font-serif text-xl text-[#123D2B]">Homeowner feedback</h2>
                    @foreach ($feedback as $item)
                        <p class="mt-3 text-sm text-[#123D2B]">{{ str_repeat('★', (int) $item->rating) }} {{ $item->title }}</p>
                        <p class="text-sm text-[#66756C]">{{ $item->comment }}</p>
                        <p class="text-xs text-[#66756C]">{{ $item->created_at->format('j M Y') }}</p>
                    @endforeach
                </article>
            @endif
        </div>
    </div>
</x-designer-layout>
