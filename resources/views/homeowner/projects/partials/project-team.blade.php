<section class="rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm">
    <h2 class="font-serif text-xl text-[#123D2B]">Project Team</h2>
    <ul class="mt-4 divide-y divide-[#f0ebe3]">
        @foreach (['designer' => 'Designer', 'contractor' => 'Contractor'] as $role => $roleLabel)
            @php
                $member = $project->{$role};
                $profile = $member?->professionalProfile;
                $subtitle = $role === 'designer'
                    ? ($profile?->title ?: 'Interior Designer')
                    : ($member?->isContractor() ? ($profile?->title ?: 'General Contractor') : 'General Contractor');
            @endphp
            <li class="py-3 first:pt-0 last:pb-0">
                @if ($member)
                    <a href="{{ route('homeowner.professionals.show', $member) }}" class="flex items-center gap-3">
                        <img src="{{ $profile?->avatarUrl() ?: $member->profile_photo_url }}" alt="" class="h-12 w-12 rounded-full object-cover">
                        <span class="min-w-0 flex-1">
                            <span class="block text-[11px] text-[#66756C]">{{ $roleLabel }}</span>
                            <span class="block truncate font-medium text-[#123D2B]">{{ $profile?->displayName() ?? $member->name }}</span>
                            <span class="block truncate text-xs text-[#66756C]">{{ $subtitle }}</span>
                            @if ($profile?->rating)
                                <span class="mt-0.5 block text-xs text-[#66756C]">
                                    <span class="text-[#C4A35A]">★</span>
                                    {{ number_format((float) $profile->rating, 1) }}
                                    @if ($profile->review_count)
                                        ({{ $profile->review_count }} reviews)
                                    @endif
                                </span>
                            @endif
                        </span>
                        <svg viewBox="0 0 20 20" class="h-4 w-4 shrink-0 text-[#66756C]" fill="none" stroke="currentColor" stroke-width="1.6"><path d="m7 4 6 6-6 6"/></svg>
                    </a>
                @else
                    <div>
                        <p class="text-[11px] text-[#66756C]">{{ $roleLabel }}</p>
                        <p class="font-medium text-[#123D2B]">Not selected</p>
                    </div>
                @endif
            </li>
        @endforeach
    </ul>
</section>
