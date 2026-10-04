<section class="rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm">
    <h3 class="font-serif text-2xl text-[#123D2B]">{{ ucfirst($role) }} Feedback</h3>
    <p class="mt-1 text-sm text-[#66756C]">{{ $person?->professionalProfile?->displayName() ?? $person?->name ?? 'Not assigned' }}</p>
    @if ($saved)
        <p class="mt-4 text-lg tracking-wide text-[#123D2B]">{{ str_repeat('★', (int) $saved->rating) }}{{ str_repeat('☆', 5 - (int) $saved->rating) }}</p>
        <p class="mt-3 font-medium text-[#123D2B]">{{ $saved->title }}</p>
        <p class="mt-2 text-sm leading-relaxed text-[#66756C]">{{ $saved->comment }}</p>
        <p class="mt-3 text-xs text-[#66756C]">Submitted {{ $saved->created_at->format('j M Y') }}</p>
    @elseif ($person)
        <form method="POST" action="{{ route('homeowner.projects.feedback.store', $project) }}" enctype="multipart/form-data" class="mt-4 space-y-3">
            @csrf
            <input type="hidden" name="role" value="{{ $role }}">
            <fieldset>
                <legend class="text-sm text-[#66756C]">Rate your experience</legend>
                <div class="mt-2 flex gap-1" data-stars>
                    <input type="hidden" name="rating" value="{{ old('role') === $role ? old('rating', 5) : 5 }}">
                    @for ($star = 1; $star <= 5; $star++)
                        <button type="button" data-value="{{ $star }}" class="text-2xl leading-none" aria-label="{{ $star }} stars">★</button>
                    @endfor
                </div>
            </fieldset>
            <label class="block text-sm">Feedback title
                <input name="title" value="{{ old('role') === $role ? old('title') : '' }}" required maxlength="150" class="mt-1 w-full rounded-2xl border border-[#ece7dc] px-3 py-2">
            </label>
            <label class="block text-sm">Comment
                <textarea name="comment" required rows="4" class="mt-1 w-full rounded-2xl border border-[#ece7dc] px-3 py-2">{{ old('role') === $role ? old('comment') : '' }}</textarea>
            </label>
            <label class="block text-sm">Optional attachment
                <input name="attachment" type="file" accept=".jpg,.jpeg,.png,.webp,.pdf" class="mt-1 block text-sm">
            </label>
            <button class="rounded-full bg-[#123D2B] px-5 py-2.5 text-sm font-medium text-white">Submit Feedback</button>
        </form>
    @else
        <p class="mt-4 text-sm text-[#66756C]">This project has no {{ $role }} to review.</p>
    @endif
</section>
<script>
    document.querySelectorAll('[data-stars]').forEach((group) => {
        if (group.dataset.ready) return;
        group.dataset.ready = '1';
        const input = group.querySelector('input');
        const paint = () => group.querySelectorAll('button').forEach((button) => {
            button.style.color = Number(button.dataset.value) <= Number(input.value) ? '#123D2B' : '#d5cbbd';
        });
        group.addEventListener('click', (event) => {
            const button = event.target.closest('button');
            if (!button) return;
            input.value = button.dataset.value;
            paint();
        });
        paint();
    });
</script>
