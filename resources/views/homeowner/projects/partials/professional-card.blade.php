@php
    $profile = $person->professionalProfile;
    $selected = $kind === 'designer' ? $project->designer_id === $person->id : $project->contractor_id === $person->id;
    $label = $kind === 'designer' ? 'Select Designer' : 'Select Contractor';
@endphp

<article class="flex h-full flex-col rounded-3xl border bg-white p-5 shadow-sm {{ $selected ? 'border-forest' : 'border-[#ece7dc]' }}">
    <div class="flex items-start gap-3">
        <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#e7f0e4] font-serif text-lg text-forest" aria-hidden="true">{{ strtoupper(substr($person->name, 0, 1)) }}</span>
        <div class="min-w-0">
            <h3 class="font-serif text-xl text-forest">{{ $profile?->displayName() ?? $person->name }}</h3>
            <p class="text-sm text-mist">{{ $profile?->title ?: ($kind === 'designer' ? 'Designer' : 'Contractor') }}@if($profile?->location) · {{ $profile->location }}@endif</p>
            @if ($profile?->specialization)
                <p class="text-sm text-charcoal">{{ $profile->specialization }}</p>
            @endif
            @if ($profile?->years_experience)
                <p class="text-sm text-mist">{{ $profile->years_experience }} years · {{ $profile->completed_projects_count }} completed</p>
            @endif
            @if ($profile?->rating !== null)
                <p class="mt-1 text-sm text-charcoal">Rating {{ $profile->rating }}</p>
            @endif
        </div>
    </div>
    <p class="mt-3 text-sm leading-relaxed text-mist">{{ $profile?->bio }}</p>
    <div class="mt-auto flex flex-wrap gap-2 pt-4">
        @if ($profile)
            <a href="{{ route('homeowner.professionals.show', ['professional' => $person, 'project' => $project->id]) }}" class="rounded-full border border-[#ddd6c8] px-4 py-2 text-sm font-medium text-charcoal transition hover:border-forest">View Portfolio</a>
        @endif
        <form method="POST" action="{{ route('homeowner.projects.team.update', $project) }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="designer_id" value="{{ $kind === 'designer' ? $person->id : $project->designer_id }}">
            <input type="hidden" name="contractor_id" value="{{ $kind === 'contractor' ? $person->id : $project->contractor_id }}">
            <button type="submit" class="rounded-full px-4 py-2 text-sm font-medium transition {{ $selected ? 'bg-[#e7f0e4] text-forest' : 'bg-forest text-ivory hover:bg-leaf' }}">{{ $selected ? 'Selected' : $label }}</button>
        </form>
    </div>
</article>
