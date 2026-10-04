<x-homeowner-layout title="Select Team">
    <p class="text-[11px] font-medium uppercase tracking-[0.18em] text-olive">{{ $project->name }}</p>
    <h1 class="mt-2 font-serif text-3xl font-medium tracking-[-0.03em] text-forest sm:text-4xl">Select Team</h1>
    <p class="mt-2 max-w-2xl text-sm text-mist">Choose a designer and a contractor for this project. Review their work before you select them.</p>

    <div class="mt-6">
        @include('homeowner.projects.partials.steps', ['current' => 4, 'project' => $project])
    </div>

    <section class="mt-8">
        <h2 class="font-serif text-2xl text-forest">Choose a Designer</h2>
        <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($designers as $designer)
                @include('homeowner.projects.partials.professional-card', ['person' => $designer, 'project' => $project, 'kind' => 'designer'])
            @empty
                <p class="text-sm text-mist">No designers are available yet.</p>
            @endforelse
        </div>
    </section>

    <section class="mt-10">
        <h2 class="font-serif text-2xl text-forest">Choose a Contractor</h2>
        <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($contractors as $contractor)
                @include('homeowner.projects.partials.professional-card', ['person' => $contractor, 'project' => $project, 'kind' => 'contractor'])
            @empty
                <p class="text-sm text-mist">No contractors are available yet.</p>
            @endforelse
        </div>
    </section>

    <div class="mt-8 flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('homeowner.projects.budget', $project) }}" class="rounded-full border border-[#ddd6c8] px-5 py-3 text-sm font-medium text-charcoal transition hover:border-forest">Back</a>
        <form method="POST" action="{{ route('homeowner.projects.team.update', $project) }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="designer_id" value="{{ $project->designer_id }}">
            <input type="hidden" name="contractor_id" value="{{ $project->contractor_id }}">
            <input type="hidden" name="complete" value="1">
            <button type="submit" class="rounded-full bg-forest px-5 py-3 text-sm font-medium text-ivory transition duration-300 hover:bg-leaf">Create Project</button>
        </form>
    </div>
</x-homeowner-layout>
