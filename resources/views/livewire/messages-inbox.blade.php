<div class="flex h-[calc(100vh-4.25rem)] min-h-[32rem] overflow-hidden bg-[#F7F4EE]">
    <aside class="{{ $selected ? 'hidden lg:flex' : 'flex' }} w-full shrink-0 flex-col border-r border-[#ece7dc] bg-white lg:w-[22.5rem]">
        <div class="flex items-center justify-between px-5 pb-3 pt-5">
            <h1 class="font-serif text-3xl text-[#123D2B]">Messages</h1>
            <button type="button" class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#123D2B] text-white" aria-label="New message">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 19.5 19 6.5M14 5h5v5M8 19H5v-3"/></svg>
            </button>
        </div>
        <div class="px-4">
            <label class="relative block">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#66756C]">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="11" cy="11" r="6.5"/><path d="m20 20-3.2-3.2"/></svg>
                </span>
                <input wire:model.live.debounce.300ms="search" type="search" placeholder="Search conversations..." class="w-full rounded-full border border-[#ece7dc] bg-[#F7F4EE] py-2.5 pl-9 pr-4 text-sm outline-none focus:border-[#123D2B]">
            </label>
            <div class="mt-3 flex gap-1 overflow-x-auto pb-1">
                @foreach (['all' => 'All', 'designers' => 'Designers', 'contractors' => 'Contractors', 'support' => 'Support'] as $key => $label)
                    <button type="button" wire:click="$set('tab', '{{ $key }}')" class="rounded-full px-3.5 py-1.5 text-sm {{ $tab === $key ? 'bg-[#123D2B] text-white' : 'text-[#66756C] hover:bg-[#F6F1E7]' }}">{{ $label }}</button>
                @endforeach
            </div>
        </div>
        <ul class="mt-2 flex-1 overflow-y-auto">
            @forelse ($conversations as $conversation)
                @php
                    $other = $conversation->counterpart(auth()->user());
                    $unread = $conversation->unreadFor(auth()->user());
                    $active = $selected?->id === $conversation->id;
                    $role = $conversation->isSupport() ? 'Support' : ($other?->isContractor() ? 'Contractor' : 'Interior Designer');
                    $preview = $conversation->last_preview ?: optional($conversation->messages->last())->body;
                    $when = $conversation->last_message_at;
                    $stamp = $when === null ? '' : ($when->isToday() ? $when->format('g:i A') : ($when->isYesterday() ? 'Yesterday' : $when->format('M j')));
                    $avatar = $conversation->isSupport()
                        ? null
                        : ($other?->professionalProfile?->avatarUrl() ?: $other?->profile_photo_url);
                @endphp
                <li>
                    <button type="button" wire:click="open({{ $conversation->id }})" class="flex w-full items-start gap-3 px-4 py-3 text-left {{ $active ? 'bg-[#E7F0E4]' : 'hover:bg-[#F7F4EE]' }}">
                        @if ($conversation->isSupport())
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#E7F0E4] text-[#123D2B]">
                                <svg viewBox="0 0 32 32" class="h-5 w-5" fill="currentColor"><path d="M16 3.2 3.4 14.1a1.2 1.2 0 0 0-.4.9V27.2A2.3 2.3 0 0 0 5.3 29.5h6.2v-8.1c0-.7.6-1.3 1.3-1.3h6.4c.7 0 1.3.6 1.3 1.3v8.1h6.2a2.3 2.3 0 0 0 2.3-2.3V15a1.2 1.2 0 0 0-.4-.9L16 3.2Z"/></svg>
                            </span>
                        @else
                            <img src="{{ $avatar }}" alt="" class="h-11 w-11 shrink-0 rounded-full object-cover">
                        @endif
                        <span class="min-w-0 flex-1">
                            <span class="flex items-center justify-between gap-2">
                                <span class="truncate font-medium text-[#123D2B]">{{ $other?->name ?? 'Conversation' }}</span>
                                <span class="shrink-0 text-[11px] text-[#66756C]">{{ $stamp }}</span>
                            </span>
                            <span class="mt-0.5 block text-xs text-[#66756C]">{{ $role }}</span>
                            <span class="mt-0.5 flex items-center justify-between gap-2">
                                <span class="truncate text-sm text-[#66756C]">{{ \Illuminate\Support\Str::limit($preview, 42) }}</span>
                                @if ($unread > 0)
                                    <span class="flex h-5 min-w-5 items-center justify-center rounded-full bg-[#2F6B49] px-1.5 text-[11px] font-medium text-white">{{ $unread }}</span>
                                @endif
                            </span>
                        </span>
                    </button>
                </li>
            @empty
                <li class="px-5 py-8 text-sm text-[#66756C]">No conversations yet. Contact a designer or contractor from their portfolio.</li>
            @endforelse
        </ul>
    </aside>

    <section class="{{ $selected ? 'flex' : 'hidden lg:flex' }} relative min-w-0 flex-1 flex-col">
        @if ($selected)
            @php
                $peer = $selected->counterpart(auth()->user());
                $role = $selected->isSupport() ? 'Support' : ($peer?->isContractor() ? 'Contractor' : 'Interior Designer');
                $project = $selected->project;
                $peerAvatar = $peer?->professionalProfile?->avatarUrl() ?: $peer?->profile_photo_url;
            @endphp
            <header class="flex items-center gap-3 border-b border-[#ece7dc] bg-white px-4 py-3">
                <button type="button" wire:click="$set('selectedId', null)" class="lg:hidden text-[#123D2B]" aria-label="Back">←</button>
                <img src="{{ $peerAvatar }}" alt="" class="h-11 w-11 rounded-full object-cover">
                <div class="min-w-0 flex-1">
                    <p class="flex items-center gap-2 font-medium text-[#123D2B]">
                        {{ $peer?->name }}
                        <span class="flex items-center gap-1 text-xs font-normal text-[#2F6B49]"><span class="h-2 w-2 rounded-full bg-[#2F6B49]"></span> Online</span>
                    </p>
                    <p class="truncate text-sm text-[#66756C]">{{ $role }}{{ $peer?->professionalProfile?->location ? ' | '.$peer->professionalProfile->location.', Sri Lanka' : '' }}</p>
                </div>
                @if ($project)
                    <div class="hidden items-center gap-3 sm:flex">
                        <img src="{{ asset('images/renova/about-interior.jpg') }}" alt="" class="h-12 w-16 rounded-xl object-cover">
                        <div class="pr-2">
                            <p class="text-[11px] text-[#66756C]">Project:</p>
                            <p class="text-sm font-medium text-[#123D2B]">{{ $project->name }}</p>
                            <p class="text-xs text-[#66756C]">Residential | {{ $project->locationLabel() ?: 'Colombo' }}</p>
                        </div>
                    </div>
                @endif
                <div class="flex items-center gap-1 text-[#123D2B]">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full" aria-hidden="true"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M6.5 4.5h3l1.5 3-2 1.5a12 12 0 0 0 6 6l1.5-2 3 1.5v3A2 2 0 0 1 17.5 19 13.5 13.5 0 0 1 5 6.5 2 2 0 0 1 6.5 4.5Z"/></svg></span>
                    <span class="flex h-9 w-9 items-center justify-center rounded-full" aria-hidden="true"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="3.5" y="6.5" width="12" height="11" rx="2"/><path d="m15.5 10.5 5-3v9l-5-3"/></svg></span>
                    <span class="flex h-9 w-9 items-center justify-center rounded-full text-lg" aria-hidden="true">⋯</span>
                </div>
            </header>

            <div class="relative flex-1 overflow-y-auto px-4 py-6 sm:px-8">
                <div class="pointer-events-none absolute inset-0 opacity-[0.05]" style="background-image: radial-gradient(#123D2B 1.1px, transparent 1.1px); background-size: 28px 28px;"></div>
                @php $day = null; @endphp
                @foreach ($selected->messages as $message)
                    @php
                        $mine = $message->sender_id === auth()->id();
                        $label = $message->created_at->isToday() ? 'Today' : $message->created_at->format('M j');
                    @endphp
                    @if ($day !== $label)
                        @php $day = $label; @endphp
                        <p class="relative mb-4 text-center text-xs text-[#66756C]">{{ $label }}</p>
                    @endif
                    <div class="relative mb-3 flex {{ $mine ? 'justify-end' : 'justify-start' }}">
                        @if (! $mine)
                            <img src="{{ $peerAvatar }}" alt="" class="mr-2 mt-1 h-8 w-8 rounded-full object-cover">
                        @endif
                        <div class="max-w-[min(100%,32rem)] rounded-2xl px-4 py-2.5 text-sm leading-relaxed shadow-sm {{ $mine ? 'rounded-br-md bg-[#DCEFDD] text-[#123D2B]' : 'rounded-bl-md bg-white text-[#18352A]' }}">
                            @if ($message->body)
                                <p class="whitespace-pre-wrap">{{ $message->body }}</p>
                            @endif
                            @if ($message->attachments->isNotEmpty())
                                <div class="mt-2 grid grid-cols-3 gap-2">
                                    @foreach ($message->attachments as $attachment)
                                        <img src="{{ $attachment->url() }}" alt="" class="h-24 w-full rounded-xl object-cover">
                                    @endforeach
                                </div>
                            @endif
                            <p class="mt-1 flex items-center justify-end gap-2 text-[11px] text-[#66756C]">
                                @if ($mine)
                                    <button type="button" wire:click="askDelete({{ $message->id }})" class="font-medium text-[#123D2B] hover:underline">Delete</button>
                                @endif
                                {{ $message->created_at->format('g:i A') }}
                                @if ($mine)
                                    <span class="text-[#2F6B49]">✓✓</span>
                                @endif
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

            <form wire:submit="send" class="border-t border-[#ece7dc] bg-white px-4 py-3">
                <div class="flex items-center gap-2">
                    <label class="flex h-10 w-10 cursor-pointer items-center justify-center rounded-full text-[#123D2B]" title="Attach">
                        <input type="file" class="sr-only" wire:model="photos" multiple accept="image/*">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M8 12.5 14.5 6A3.5 3.5 0 1 1 19.4 11l-8.2 8.2a4.5 4.5 0 0 1-6.4-6.4L13 4.6"/></svg>
                    </label>
                    <label class="flex h-10 w-10 cursor-pointer items-center justify-center rounded-full text-[#123D2B]" title="Photo">
                        <input type="file" class="sr-only" wire:model="photos" multiple accept="image/*">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="3.5" y="6" width="17" height="13" rx="2"/><circle cx="9" cy="11" r="1.4"/><path d="m8 16 3.2-3.2L14 15l2-2 3.5 3"/></svg>
                    </label>
                    <input wire:model="body" type="text" placeholder="Type a message..." class="min-w-0 flex-1 rounded-full border border-[#ece7dc] bg-[#F7F4EE] px-4 py-2.5 text-sm outline-none focus:border-[#123D2B]">
                    <button type="submit" class="flex h-11 w-11 items-center justify-center rounded-full bg-[#123D2B] text-white" aria-label="Send">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="currentColor"><path d="M3.4 11.2 20.1 3.8c.7-.3 1.4.4 1.1 1.1l-7.4 16.7c-.3.7-1.3.7-1.6 0l-2.6-6.2-6.2-2.6c-.7-.3-.7-1.3 0-1.6Z"/></svg>
                    </button>
                </div>
                @if (count($photos) > 0)
                    <p class="mt-2 text-xs text-[#66756C]">{{ count($photos) }} photo{{ count($photos) === 1 ? '' : 's' }} ready to send.</p>
                @endif
            </form>
            @if ($confirmingDeleteId)
                <div class="absolute inset-0 z-10 flex items-center justify-center bg-[#123D2B]/30 px-4">
                    <div class="w-full max-w-sm rounded-3xl bg-white p-6 text-center shadow-xl" role="dialog" aria-labelledby="delete-message-title">
                        <h2 id="delete-message-title" class="font-serif text-2xl text-[#123D2B]">Delete message?</h2>
                        <p class="mt-2 text-sm text-[#66756C]">This removes the message you sent. Other people's messages stay in the conversation.</p>
                        <div class="mt-5 flex justify-center gap-3">
                            <button type="button" wire:click="cancelDelete" class="rounded-full border border-[#ece7dc] px-5 py-2 text-sm text-[#123D2B]">Cancel</button>
                            <button type="button" wire:click="deleteMessage" class="rounded-full bg-[#123D2B] px-5 py-2 text-sm font-medium text-white">Delete</button>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="flex flex-1 items-center justify-center text-sm text-[#66756C]">Select a conversation to start messaging.</div>
        @endif
    </section>
</div>
