<x-contractor-layout title="Profile">
    <h1 class="rh-serif text-4xl text-[#123D2B]">Contractor Profile</h1>
    <form method="POST" action="{{ route('contractor.profile.update') }}" enctype="multipart/form-data" class="mt-6 grid max-w-3xl gap-3 rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm">
        @csrf
        @method('PUT')
        <label class="text-sm text-[#66756C]">Name<input name="name" value="{{ old('name', $user->name) }}" class="mt-1 w-full rounded-2xl border border-[#ddd6c8] px-3 py-2 text-[#123D2B]" required></label>
        <label class="text-sm text-[#66756C]">Company<input name="business_name" value="{{ old('business_name', $profile?->business_name) }}" class="mt-1 w-full rounded-2xl border border-[#ddd6c8] px-3 py-2"></label>
        <label class="text-sm text-[#66756C]">Title<input name="title" value="{{ old('title', $profile?->title) }}" class="mt-1 w-full rounded-2xl border border-[#ddd6c8] px-3 py-2"></label>
        <label class="text-sm text-[#66756C]">Location<input name="location" value="{{ old('location', $profile?->location) }}" class="mt-1 w-full rounded-2xl border border-[#ddd6c8] px-3 py-2"></label>
        <label class="text-sm text-[#66756C]">Experience (years)<input type="number" name="years_experience" value="{{ old('years_experience', $profile?->years_experience) }}" class="mt-1 w-full rounded-2xl border border-[#ddd6c8] px-3 py-2"></label>
        <label class="text-sm text-[#66756C]">Specialties<input name="specialization" value="{{ old('specialization', $profile?->specialization) }}" class="mt-1 w-full rounded-2xl border border-[#ddd6c8] px-3 py-2"></label>
        <label class="text-sm text-[#66756C]">About<textarea name="about" rows="4" class="mt-1 w-full rounded-2xl border border-[#ddd6c8] px-3 py-2">{{ old('about', $profile?->about) }}</textarea></label>
        <label class="text-sm text-[#66756C]">Profile image<input type="file" name="avatar" accept="image/*" class="mt-1 text-sm"></label>
        <button class="w-fit rounded-full bg-[#123D2B] px-4 py-2 text-sm text-white">Save profile</button>
    </form>
    <section class="mt-8">
        <h2 class="rh-serif text-2xl text-[#123D2B]">Portfolio</h2>
        <div class="mt-4 grid gap-4 md:grid-cols-2">
            @foreach ($profile?->caseStudies ?? [] as $case)
                <a href="{{ route('contractor.profile.project', $case) }}" class="overflow-hidden rounded-[1.4rem] border border-[#ece7dc] bg-white shadow-sm">
                    <img src="{{ $case->heroUrl() }}" alt="" class="h-40 w-full object-cover">
                    <div class="p-4">
                        <p class="font-medium text-[#123D2B]">{{ $case->title }}</p>
                        <p class="text-sm text-[#66756C]">{{ $case->location }}</p>
                    </div>
                </a>
            @endforeach
        </div>
        <form method="POST" action="{{ route('contractor.profile.portfolio.store') }}" enctype="multipart/form-data" class="mt-4 grid max-w-3xl gap-3 rounded-[1.4rem] border border-[#ece7dc] bg-white p-5">
            @csrf
            <input name="title" placeholder="Portfolio project" class="rounded-2xl border border-[#ddd6c8] px-3 py-2 text-sm" required>
            <input name="location" placeholder="Location" class="rounded-2xl border border-[#ddd6c8] px-3 py-2 text-sm">
            <textarea name="summary" rows="2" placeholder="Summary" class="rounded-2xl border border-[#ddd6c8] px-3 py-2 text-sm"></textarea>
            <input type="file" name="images[]" accept="image/*" multiple required>
            <button class="w-fit rounded-full bg-[#123D2B] px-4 py-2 text-sm text-white">Add portfolio project</button>
        </form>
    </section>
</x-contractor-layout>
