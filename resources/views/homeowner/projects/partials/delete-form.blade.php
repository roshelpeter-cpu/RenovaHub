<div class="{{ str_contains($class ?? '', 'block') ? '' : 'inline-flex' }}">
    <button type="button" class="{{ $class ?? 'rounded-full border border-red-200 px-4 py-2 text-sm font-medium text-red-700 transition hover:bg-red-50' }}" onclick="this.nextElementSibling.showModal()">{{ $buttonLabel ?? 'Delete' }}</button>
    <dialog class="w-[min(28rem,calc(100%-2rem))] rounded-3xl border border-[#ece7dc] bg-[#F6F1E7] p-6 text-charcoal shadow-xl backdrop:bg-[#123D2B]/40">
        <form method="POST" action="{{ route('homeowner.projects.destroy', $project) }}">
            @csrf
            @method('DELETE')
            <h2 class="font-serif text-2xl text-forest">Delete this project?</h2>
            <p class="mt-2 text-sm leading-relaxed text-mist">Deleting removes this project from your project list, including its tasks, documents, quotations and messages. This cannot be undone.</p>
            <div class="mt-5 flex justify-end gap-2">
                <button type="button" class="rounded-full border border-[#ddd6c8] px-4 py-2 text-sm" onclick="this.closest('dialog').close()">Cancel</button>
                <button type="submit" class="rounded-full bg-red-700 px-4 py-2 text-sm font-medium text-white">Delete project</button>
            </div>
        </form>
    </dialog>
</div>
