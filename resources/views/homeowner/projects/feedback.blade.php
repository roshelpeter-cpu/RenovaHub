<x-homeowner-layout :title="$project->name.' feedback'" :flush="true" :canvas="true">
    <x-project-context :project="$project" section="feedback">
        <h2 class="font-serif text-3xl text-[#123D2B]">Project Feedback</h2>
        <p class="mt-2 max-w-2xl text-sm text-[#66756C]">Rate the designer and the contractor separately. This is available once the project is complete.</p>
        <div class="mt-6 grid gap-6 lg:grid-cols-2">
            @include('homeowner.projects.partials.feedback-form', ['role' => 'designer', 'saved' => $designerFeedback, 'person' => $project->designer])
            @include('homeowner.projects.partials.feedback-form', ['role' => 'contractor', 'saved' => $contractorFeedback, 'person' => $project->contractor])
        </div>
    </x-project-context>
</x-homeowner-layout>
