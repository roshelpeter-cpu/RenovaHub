<x-contractor-layout :title="$caseStudy->title">
    <h1 class="rh-serif text-4xl text-[#123D2B]">{{ $caseStudy->title }}</h1>
    <p class="mt-2 text-sm text-[#66756C]">{{ $caseStudy->location }}</p>
    <p class="mt-4 max-w-3xl text-sm text-[#66756C]">{{ $caseStudy->summary }}</p>
    <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($caseStudy->images as $image)
            <img src="{{ asset($image->path) }}" alt="" class="h-48 w-full rounded-2xl object-cover">
        @endforeach
    </div>
</x-contractor-layout>
