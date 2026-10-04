<x-homeowner-layout :title="$project->name" :flush="true" :canvas="true">
    <x-project-context :project="$project" section="overview">
        @include('homeowner.projects.partials.overview', ['project' => $project])
    </x-project-context>
</x-homeowner-layout>
