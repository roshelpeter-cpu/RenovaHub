<x-designer-layout title="{{ $project->title }}">
    <p class="text-sm text-[#66756C]"><a href="{{ route('designer.profile.edit') }}" class="hover:text-[#123D2B]">Profile</a> › Portfolio</p>
    <h1 class="mt-2 font-serif text-4xl text-[#123D2B]">{{ $project->title }}</h1>
    <p class="mt-2 text-sm text-[#66756C]">{{ $project->project_type }} · {{ $project->location }}</p>
    <p class="mt-4 max-w-3xl text-sm leading-relaxed text-[#66756C]">{{ $project->overview ?: $project->summary }}</p>
    <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($project->images as $image)
            <img src="{{ $image->url() }}" alt="{{ $image->caption }}" class="h-48 w-full rounded-2xl object-cover">
        @endforeach
        @if ($project->images->isEmpty())
            <img src="{{ $project->heroUrl() }}" alt="" class="h-48 w-full rounded-2xl object-cover">
        @endif
    </div>
</x-designer-layout>
