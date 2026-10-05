<section class="rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm">
    <h2 class="font-serif text-xl text-[#123D2B]">Project Team</h2>
    <ul class="mt-4 divide-y divide-[#f0ebe3]">
        @foreach (['designer' => 'Designer', 'contractor' => 'Contractor'] as $role => $roleLabel)
            @php
                $slot = $project->teamPresentation($role);
                $member = $slot['user'];
                $profile = $member?->professionalProfile;
                $subtitle = $role === 'designer'
                    ? ($profile?->title ?: 'Interior Designer')
                    : ($profile?->title ?: 'General Contractor');
                $badge = match ($slot['state']) {
                    'pending' => 'bg-[#F6F1E7] text-[#8A6A2F]',
                    'declined' => 'bg-[#F8E8E4] text-[#8A3B2A]',
                    'accepted' => 'bg-[#E7F0E4] text-[#123D2B]',
                    default => 'bg-[#F7F4EE] text-[#66756C]',
                };
            @endphp
            <li class="py-3 first:pt-0 last:pb-0">
                @if ($member && $slot['state'] === 'accepted')
                    <a href="{{ route('homeowner.professionals.show', $member) }}" class="flex items-center gap-3">
                        <img src="{{ $profile?->avatarUrl() ?: $member->profile_photo_url }}" alt="" class="h-12 w-12 rounded-full object-cover">
                        <span class="min-w-0 flex-1">
                            <span class="block text-[11px] text-[#66756C]">{{ $roleLabel }}</span>
                            <span class="block truncate font-medium text-[#123D2B]">{{ $profile?->displayName() ?? $member->name }}</span>
                            <span class="block truncate text-xs text-[#66756C]">{{ $subtitle }}</span>
                        </span>
                        <span class="rounded-full px-2.5 py-1 text-[11px] {{ $badge }}">{{ $slot['label'] }}</span>
                    </a>
                @elseif ($member)
                    <div class="flex items-center gap-3">
                        <img src="{{ $profile?->avatarUrl() ?: $member->profile_photo_url }}" alt="" class="h-12 w-12 rounded-full object-cover">
                        <span class="min-w-0 flex-1">
                            <span class="block text-[11px] text-[#66756C]">{{ $roleLabel }}</span>
                            <span class="block truncate font-medium text-[#123D2B]">{{ $profile?->displayName() ?? $member->name }}</span>
                            <span class="mt-1 inline-flex rounded-full px-2.5 py-1 text-[11px] {{ $badge }}">{{ $slot['label'] }}</span>
                            @if ($slot['state'] === 'declined')
                                <a href="{{ route('homeowner.projects.team', $project) }}" class="mt-1 block text-xs font-medium text-[#8A3B2A]">Select another {{ strtolower($roleLabel) }}</a>
                            @endif
                        </span>
                    </div>
                @else
                    <div>
                        <p class="text-[11px] text-[#66756C]">{{ $roleLabel }}</p>
                        <p class="font-medium text-[#123D2B]">Not selected</p>
                        <a href="{{ route('homeowner.projects.team', $project) }}" class="mt-1 inline-block text-xs text-[#123D2B] underline">Choose a {{ strtolower($roleLabel) }}</a>
                    </div>
                @endif
            </li>
        @endforeach
    </ul>
</section>
