<x-homeowner-layout :title="$stage">
    <p class="text-[11px] font-medium uppercase tracking-[0.18em] text-olive">{{ $project->name }}</p>
    <h1 class="mt-2 font-serif text-3xl font-medium tracking-[-0.03em] text-forest sm:text-4xl">{{ $stage }}</h1>
    <div class="mt-6">
        @include('homeowner.projects.partials.steps', ['current' => 3])
    </div>
    <div class="mt-6 max-w-2xl rounded-3xl border border-dashed border-olive/50 bg-white p-6 shadow-sm">
        <p class="font-medium text-charcoal">{{ $stage }} is not available yet.</p>
        <p class="mt-2 text-sm leading-relaxed text-mist">This stage is only a navigation marker. RenovaHub is not collecting budget, timeline or team information in this release.</p>
        <a href="{{ route('homeowner.projects.location', $project) }}" class="mt-5 inline-flex rounded-full bg-forest px-5 py-3 text-sm font-medium text-ivory transition hover:bg-leaf">Back to location</a>
    </div>
</x-homeowner-layout>
