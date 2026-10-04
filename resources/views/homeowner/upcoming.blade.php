<x-homeowner-layout :title="$title">
    <p class="text-[11px] font-medium uppercase tracking-[0.18em] text-olive">Homeowner workspace</p>
    <h1 class="mt-2 font-serif text-3xl font-medium tracking-[-0.03em] text-forest sm:text-4xl">{{ $title }}</h1>
    <div class="mt-6 max-w-2xl rounded-3xl border border-dashed border-olive/50 bg-white p-6 shadow-sm">
        <p class="font-medium text-charcoal">{{ $title }} is not available yet.</p>
        <p class="mt-2 text-sm leading-relaxed text-mist">This section is in the navigation so the workspace stays familiar. It does not store or display sample records.</p>
        <a href="{{ route('homeowner.projects.index') }}" class="mt-5 inline-flex rounded-full bg-forest px-5 py-3 text-sm font-medium text-ivory transition hover:bg-leaf">Go to My Projects</a>
    </div>
</x-homeowner-layout>
