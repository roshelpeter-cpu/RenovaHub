<x-designer-layout title="Profile">
    <h1 class="font-serif text-4xl text-[#123D2B] sm:text-5xl">{{ auth()->user()->name }}</h1>
    <p class="mt-1 text-sm text-[#66756C]">{{ $profile->title }} · {{ $profile->location }} · Designer</p>
    <div class="mt-6 grid gap-6 lg:grid-cols-[16rem_1fr]">
        <img src="{{ $profile->avatarUrl() ?: auth()->user()->profile_photo_url }}" alt="" class="h-48 w-48 rounded-[1.4rem] object-cover">
        <form method="POST" action="{{ route('designer.profile.update') }}" enctype="multipart/form-data" class="grid gap-3 rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm">
            @csrf
            @method('PUT')
            <h2 class="font-serif text-2xl text-[#123D2B]">About Me</h2>
            <label class="text-sm">Name<input name="name" value="{{ auth()->user()->name }}" required class="mt-1 w-full rounded-2xl border border-[#ece7dc] px-3 py-2"></label>
            <label class="text-sm">Professional title<input name="title" value="{{ $profile->title }}" required class="mt-1 w-full rounded-2xl border border-[#ece7dc] px-3 py-2"></label>
            <label class="text-sm">Location<input name="location" value="{{ $profile->location }}" required class="mt-1 w-full rounded-2xl border border-[#ece7dc] px-3 py-2"></label>
            <label class="text-sm">Specializations<input name="specialization" value="{{ $profile->specialization }}" required class="mt-1 w-full rounded-2xl border border-[#ece7dc] px-3 py-2"></label>
            <label class="text-sm">Years of experience<input type="number" name="years_experience" value="{{ $profile->years_experience }}" required class="mt-1 w-full rounded-2xl border border-[#ece7dc] px-3 py-2"></label>
            <label class="text-sm">Bio<textarea name="bio" required class="mt-1 w-full rounded-2xl border border-[#ece7dc] px-3 py-2">{{ $profile->bio }}</textarea></label>
            <label class="text-sm">Professional description<textarea name="about" class="mt-1 w-full rounded-2xl border border-[#ece7dc] px-3 py-2">{{ $profile->about }}</textarea></label>
            <label class="text-sm">Profile image<input type="file" name="photo" accept="image/*"></label>
            <button class="w-fit rounded-full bg-[#123D2B] px-4 py-2 text-sm text-white">Save profile</button>
        </form>
    </div>
    <section class="mt-8">
        <h2 class="font-serif text-2xl text-[#123D2B]">Portfolio Projects</h2>
        <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($profile->caseStudies as $case)
                <a href="{{ route('designer.profile.project', $case) }}" class="overflow-hidden rounded-[1.4rem] border border-[#ece7dc] bg-white shadow-sm">
                    <img src="{{ $case->heroUrl() }}" alt="" class="h-40 w-full object-cover">
                    <div class="p-4">
                        <h3 class="font-serif text-xl text-[#123D2B]">{{ $case->title }}</h3>
                        <p class="text-xs text-[#66756C]">{{ $case->project_type }} · {{ $case->location }}</p>
                        <p class="mt-2 text-sm text-[#66756C]">{{ \Illuminate\Support\Str::limit($case->summary, 120) }}</p>
                    </div>
                </a>
            @endforeach
        </div>
        <form method="POST" action="{{ route('designer.profile.portfolio.store') }}" enctype="multipart/form-data" class="mt-4 grid gap-3 rounded-[1.4rem] border border-[#ece7dc] bg-white p-4 md:grid-cols-2">
            @csrf
            <input name="title" required placeholder="Project name" class="rounded-2xl border border-[#ece7dc] px-3 py-2 text-sm">
            <input name="project_type" required placeholder="Project type" class="rounded-2xl border border-[#ece7dc] px-3 py-2 text-sm">
            <input name="location" required placeholder="Location" class="rounded-2xl border border-[#ece7dc] px-3 py-2 text-sm">
            <input type="file" name="images[]" accept="image/*" multiple required>
            <textarea name="summary" required placeholder="Description" class="rounded-2xl border border-[#ece7dc] px-3 py-2 text-sm md:col-span-2"></textarea>
            <button class="w-fit rounded-full bg-[#123D2B] px-4 py-2 text-sm text-white">Add portfolio project</button>
        </form>
    </section>
    <section class="mt-8">
        <h2 class="font-serif text-2xl text-[#123D2B]">Ratings & Reviews</h2>
        <p class="mt-1 text-sm text-[#66756C]">{{ $profile->rating ? number_format((float) $profile->rating, 1) : 'No rating yet' }}</p>
        <div class="mt-4 space-y-3">
            @forelse ($feedback as $item)
                <article class="rounded-2xl border border-[#ece7dc] bg-white p-4">
                    <p class="text-sm text-[#123D2B]">{{ str_repeat('★', (int) $item->rating) }} {{ $item->title }}</p>
                    <p class="mt-1 text-sm text-[#66756C]">{{ $item->comment }}</p>
                    <p class="mt-1 text-xs text-[#66756C]">{{ $item->project?->name }} · {{ $item->created_at->format('j M Y') }}</p>
                </article>
            @empty
                @foreach ($profile->reviews as $review)
                    <article class="rounded-2xl border border-[#ece7dc] bg-white p-4">
                        <p class="text-sm text-[#123D2B]">{{ $review->author_name }} · {{ $review->rating }}</p>
                        <p class="mt-1 text-sm text-[#66756C]">{{ $review->body }}</p>
                    </article>
                @endforeach
            @endforelse
        </div>
    </section>
</x-designer-layout>
