<x-homeowner-layout :title="$project->name" :flush="true" :canvas="true">
    <div class="mx-auto max-w-[90rem] px-4 py-6 sm:px-6 lg:px-8">
        <p class="text-sm text-[#66756C]">
            <a href="{{ route('homeowner.home') }}" class="hover:text-[#123D2B]">Home</a>
            <span class="mx-1.5 text-[#c9c2b4]">›</span>
            <a href="{{ route('homeowner.projects.index') }}" class="hover:text-[#123D2B]">My Projects</a>
            <span class="mx-1.5 text-[#c9c2b4]">›</span>
            <span class="text-[#123D2B]">{{ $project->name }}</span>
        </p>

        <div class="mt-6 grid items-start gap-6 xl:grid-cols-12">
            <div class="xl:col-span-7">
                <livewire:project-gallery :project-id="$project->id" :show-overview-grid="false" :key="'hero-'.$project->id" />
            </div>
            <div class="xl:col-span-5">
                @include('homeowner.projects.partials.header', ['project' => $project])
            </div>
        </div>

        <div class="mt-6 grid items-start gap-6 xl:grid-cols-3">
            @include('homeowner.projects.partials.project-team', ['project' => $project])
            @include('homeowner.projects.partials.budget', ['project' => $project])
            @include('homeowner.projects.partials.timeline', ['project' => $project])
        </div>

        @include('homeowner.projects.partials.tabs', ['project' => $project, 'section' => $section ?? 'overview'])

        <div class="mt-8">
            @include('homeowner.projects.partials.overview', ['project' => $project])
        </div>
    </div>
</x-homeowner-layout>
