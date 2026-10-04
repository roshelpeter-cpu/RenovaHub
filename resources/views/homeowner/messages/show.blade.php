<x-homeowner-layout title="Messages">
    @include('homeowner.projects.partials.tabs', ['project' => $project])
    <h1 class="mt-4 font-serif text-3xl text-forest">{{ $project->name }}</h1>
    <p class="text-sm text-mist">Homeowner, designer and contractor</p>
    <div class="mt-6 max-w-3xl space-y-3">
        @forelse ($project->messages as $message)
            <article class="rounded-3xl border border-[#ece7dc] px-4 py-3 {{ $message->sender_id === auth()->id() ? 'bg-[#F6F1E7]' : 'bg-white' }}">
                <p class="text-xs text-mist">{{ $message->sender?->name }} · {{ $message->created_at->format('j M Y, H:i') }}</p>
                <p class="mt-1 text-sm leading-relaxed text-charcoal">{{ $message->body }}</p>
                @foreach ($message->attachments as $attachment)
                    <p class="mt-1 text-xs text-forest">{{ $attachment->original_name }}</p>
                @endforeach
            </article>
        @empty
            @include('homeowner.partials.empty', ['title' => 'No messages', 'body' => 'Start the conversation with your designer or contractor.'])
        @endforelse
    </div>
    <form method="POST" action="{{ route('homeowner.projects.messages.store', $project) }}" enctype="multipart/form-data" class="mt-4 max-w-3xl rounded-3xl border border-[#ece7dc] bg-white p-4">
        @csrf
        <label for="body" class="sr-only">Message</label>
        <textarea id="body" name="body" required rows="3" placeholder="Write a message..." class="w-full rounded-2xl border border-line px-3 py-2 text-sm"></textarea>
        <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
            <input name="attachment" type="file" accept=".jpg,.jpeg,.png,.webp,.pdf" class="text-sm">
            <button class="rounded-full bg-forest px-5 py-2 text-sm font-medium text-ivory">Send</button>
        </div>
        @error('body') <p class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
    </form>
</x-homeowner-layout>
