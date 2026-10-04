<x-homeowner-layout :title="$profile->displayName().' projects'" :flush="true">
    <div class="mx-auto max-w-[90rem] px-4 py-6 sm:px-6 lg:px-8">
        <p class="text-sm text-[#66756C]">
            <a href="{{ route('homeowner.home') }}" class="hover:text-[#123D2B]">Home</a>
            <span class="mx-1 text-[#c9c2b4]">›</span>
            <a href="{{ route('homeowner.explore') }}" class="hover:text-[#123D2B]">Professionals</a>
            <span class="mx-1 text-[#c9c2b4]">›</span>
            <a href="{{ route('homeowner.professionals.show', $professional) }}" class="hover:text-[#123D2B]">{{ $profile->displayName() }}</a>
            <span class="mx-1 text-[#c9c2b4]">›</span>
            Projects
        </p>

        <div class="mt-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h1 class="font-serif text-4xl text-[#123D2B]">{{ $profile->displayName() }}</h1>
                <p class="mt-1 text-sm text-[#66756C]">{{ $profile->title }} · {{ $profile->location }}, Sri Lanka</p>
            </div>
            <form method="POST" action="{{ route('homeowner.professionals.contact', $professional) }}">
                @csrf
                <button type="submit" class="rounded-full bg-[#123D2B] px-5 py-2.5 text-sm font-medium text-white">Contact {{ $professional->isDesigner() ? 'Designer' : 'Contractor' }}</button>
            </form>
        </div>

        <section class="mt-8 max-w-3xl">
            <h2 class="font-serif text-2xl text-[#123D2B]">{{ $professional->isContractor() ? 'About the Company' : 'About Me' }}</h2>
            <p class="mt-3 whitespace-pre-line text-sm leading-relaxed text-[#66756C]">{{ $profile->aboutText() }}</p>
        </section>

        <section class="mt-10">
            <h2 class="font-serif text-2xl text-[#123D2B]">Projects</h2>
            <div class="mt-5 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($caseStudies as $item)
                    <a href="{{ route('homeowner.professionals.project', [$professional, $item->slug]) }}" class="overflow-hidden rounded-[1.5rem] border border-[#ece7dc] bg-white shadow-sm">
                        <img src="{{ $item->heroUrl() }}" alt="" class="h-64 w-full object-cover">
                        <div class="p-5">
                            <h3 class="font-serif text-2xl text-[#123D2B]">{{ $item->title }}</h3>
                            <p class="mt-1 text-sm text-[#66756C]">{{ $item->location }} · {{ $item->project_type }}</p>
                            <p class="mt-1 text-xs text-[#66756C]">{{ $item->completed_on?->format('M Y') ? 'Completed '.$item->completed_on->format('M Y') : 'Completed project' }}</p>
                            <p class="mt-3 text-sm leading-relaxed text-[#66756C]">{{ $item->summary }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    </div>
</x-homeowner-layout>
