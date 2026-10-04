<x-guest-layout>
    <div class="mx-auto max-w-lg px-6 py-16">
        <h1 class="font-serif text-3xl text-forest">Project invitation</h1>
        <p class="mt-3 text-sm leading-relaxed text-charcoal">{{ $invitation->project->name }} has invited you as the {{ $invitation->role }}.</p>
        <form method="POST" action="{{ route('invitations.respond', $invitation) }}" class="mt-6 flex gap-3">
            @csrf
            <button name="decision" value="accepted" class="rounded-full bg-forest px-5 py-3 text-sm text-ivory">Accept</button>
            <button name="decision" value="declined" class="rounded-full border border-red-200 px-5 py-3 text-sm text-red-700">Decline</button>
        </form>
    </div>
</x-guest-layout>
