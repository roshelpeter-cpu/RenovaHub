<div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
    <div>
        <h1 class="font-serif text-4xl text-[#123D2B]">Documents</h1>
        <p class="mt-2 text-sm text-[#66756C]">Design concepts, plans, renders and the final package. Contractor quotations and invoices are not listed here.</p>
    </div>
    <form method="GET" class="flex flex-wrap gap-2">
        @unless($project)
            <select name="project" class="rounded-2xl border border-[#ece7dc] bg-white px-3 py-2 text-sm" onchange="this.form.requestSubmit()">
                <option value="">All Projects</option>
                @foreach ($projects as $option)
                    <option value="{{ $option->id }}" @selected(request('project') == $option->id)>{{ $option->name }}</option>
                @endforeach
            </select>
        @endunless
        <select name="category" class="rounded-2xl border border-[#ece7dc] bg-white px-3 py-2 text-sm" onchange="this.form.requestSubmit()">
            <option value="">All types</option>
            @foreach (['design' => 'Design Concepts', 'floor_plans' => 'Floor Plans', 'technical' => 'Renders', 'materials' => 'Material Specifications', 'final_design' => 'Final Design', 'other' => 'Other'] as $value => $label)
                <option value="{{ $value }}" @selected($category === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </form>
</div>
@php $uploadProject = $project ?? $projects->first(fn ($item) => ! $item->isClosedRecord()); @endphp
@if ($uploadProject && auth()->user()->can('uploadDesign', [\App\Models\Document::class, $uploadProject]))
    <form method="POST" action="{{ route('designer.documents.store') }}" enctype="multipart/form-data" class="mt-4 grid gap-3 rounded-[1.4rem] border border-[#ece7dc] bg-white p-4 shadow-sm md:grid-cols-5">
        @csrf
        @if ($project)
            <input type="hidden" name="project_id" value="{{ $project->id }}">
        @else
            <select name="project_id" class="rounded-2xl border border-[#ece7dc] px-3 py-2 text-sm">
                @foreach ($projects as $option)
                    @continue($option->isClosedRecord())
                    <option value="{{ $option->id }}">{{ $option->name }}</option>
                @endforeach
            </select>
        @endif
        <input name="name" required placeholder="Document name" class="rounded-2xl border border-[#ece7dc] px-3 py-2 text-sm">
        <select name="category" class="rounded-2xl border border-[#ece7dc] px-3 py-2 text-sm">
            @foreach (\App\Models\Document::designerCategories() as $value)
                <option value="{{ $value }}">{{ \App\Models\Document::categories()[$value] ?? $value }}</option>
            @endforeach
        </select>
        <input type="file" name="file" required class="text-sm">
        <button class="rounded-full bg-[#123D2B] px-4 py-2 text-sm text-white">Upload</button>
    </form>
@endif
<div class="mt-4 overflow-x-auto rounded-[1.4rem] border border-[#ece7dc] bg-white shadow-sm">
    <table class="min-w-full text-left text-sm">
        <thead class="text-xs text-[#66756C]"><tr><th class="px-4 py-3">Document</th><th class="px-4 py-3">Project</th><th class="px-4 py-3">Type</th><th class="px-4 py-3">Date</th><th class="px-4 py-3">Actions</th></tr></thead>
        <tbody>
            @forelse ($documents as $document)
                <tr class="border-t border-[#ece7dc]">
                    <td class="px-4 py-3 text-[#123D2B]">{{ $document->name }}</td>
                    <td class="px-4 py-3"><a class="hover:underline" href="{{ route('designer.projects.show', $document->project) }}">{{ $document->project->name }}</a></td>
                    <td class="px-4 py-3">{{ $document->categoryLabel() }}</td>
                    <td class="px-4 py-3">{{ $document->created_at->format('j M Y') }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('designer.documents.show', $document) }}" class="text-[#123D2B]">View</a>
                        <a href="{{ route('designer.documents.download', $document) }}" class="ml-2 text-[#123D2B]">Download</a>
                        @can('delete', $document)
                            <form method="POST" action="{{ route('designer.documents.destroy', $document) }}" class="inline">@csrf @method('DELETE')<button class="ml-2 text-[#8A3B2A]">Delete</button></form>
                        @endcan
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-6 text-[#66756C]">No design documents yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $documents->links() }}</div>
