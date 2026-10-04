<x-homeowner-layout :title="$project ? $project->name.' documents' : 'My Documents'" :canvas="true">
    @if ($project)
        <p class="text-sm text-[#66756C]">
            <a href="{{ route('homeowner.home') }}" class="hover:text-[#123D2B]">Home</a>
            <span class="mx-1.5 text-[#c9c2b4]">›</span>
            <a href="{{ route('homeowner.projects.show', $project) }}" class="hover:text-[#123D2B]">{{ $project->name }}</a>
            <span class="mx-1.5 text-[#c9c2b4]">›</span>
            Documents
        </p>
        @include('homeowner.projects.partials.tabs', ['project' => $project])
        <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="font-serif text-4xl text-[#123D2B]">Documents</h1>
                <p class="mt-2 text-sm text-[#66756C]">Documents for this project only.</p>
            </div>
        </div>
        <form method="POST" action="{{ route('homeowner.projects.documents.store', $project) }}" enctype="multipart/form-data" class="mt-6 grid gap-3 rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm md:grid-cols-2 xl:grid-cols-5">
            @csrf
            <label class="block text-sm"><span class="mb-1 block text-xs text-[#66756C]">Name</span><input name="name" required maxlength="150" class="w-full rounded-2xl border border-[#ece7dc] px-3 py-2 text-sm"></label>
            <label class="block text-sm"><span class="mb-1 block text-xs text-[#66756C]">Type</span>
                <select name="category" class="w-full rounded-2xl border border-[#ece7dc] px-3 py-2 text-sm">
                    @foreach (\App\Models\Document::categories() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block text-sm md:col-span-2 xl:col-span-1"><span class="mb-1 block text-xs text-[#66756C]">Description</span><input name="description" maxlength="500" class="w-full rounded-2xl border border-[#ece7dc] px-3 py-2 text-sm"></label>
            <label class="block text-sm"><span class="mb-1 block text-xs text-[#66756C]">File</span><input name="file" type="file" required accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx" class="w-full text-sm"></label>
            <div class="flex items-end"><button type="submit" class="rounded-full bg-[#123D2B] px-4 py-2 text-sm font-medium text-white">Upload Document</button></div>
            @error('file') <p class="text-sm text-red-700 md:col-span-2 xl:col-span-5">{{ $message }}</p> @enderror
        </form>
    @else
        <p class="text-sm text-[#66756C]">
            <a href="{{ route('homeowner.home') }}" class="hover:text-[#123D2B]">Home</a>
            <span class="mx-1.5 text-[#c9c2b4]">›</span>
            Documents
        </p>
        <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="font-serif text-4xl text-[#123D2B] sm:text-5xl">My Documents</h1>
                <p class="mt-2 max-w-2xl text-sm text-[#66756C]">Access and manage documents from all your renovation projects.</p>
            </div>
            <button type="button" class="rounded-full bg-[#123D2B] px-4 py-2 text-sm font-medium text-white" onclick="document.getElementById('upload-document').classList.toggle('hidden')">+ Upload Document</button>
        </div>

        <form id="upload-document" method="POST" action="{{ route('homeowner.documents.store') }}" enctype="multipart/form-data" class="mt-6 hidden grid gap-3 rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm md:grid-cols-2 xl:grid-cols-6">
            @csrf
            <label class="block text-sm"><span class="mb-1 block text-xs text-[#66756C]">Project</span>
                <select name="project_id" required class="w-full rounded-2xl border border-[#ece7dc] px-3 py-2 text-sm">
                    <option value="">Select a project</option>
                    @foreach ($projects as $option)
                        <option value="{{ $option->id }}" @selected((int) old('project_id', $filters['project'] ?? 0) === $option->id)>{{ $option->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block text-sm"><span class="mb-1 block text-xs text-[#66756C]">Name</span><input name="name" value="{{ old('name') }}" required maxlength="150" class="w-full rounded-2xl border border-[#ece7dc] px-3 py-2 text-sm"></label>
            <label class="block text-sm"><span class="mb-1 block text-xs text-[#66756C]">Type</span>
                <select name="category" class="w-full rounded-2xl border border-[#ece7dc] px-3 py-2 text-sm">
                    @foreach (\App\Models\Document::categories() as $value => $label)
                        <option value="{{ $value }}" @selected(old('category') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block text-sm"><span class="mb-1 block text-xs text-[#66756C]">Description</span><input name="description" value="{{ old('description') }}" maxlength="500" class="w-full rounded-2xl border border-[#ece7dc] px-3 py-2 text-sm"></label>
            <label class="block text-sm"><span class="mb-1 block text-xs text-[#66756C]">File</span><input name="file" type="file" required accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx" class="w-full text-sm"></label>
            <div class="flex items-end"><button class="rounded-full bg-[#123D2B] px-4 py-2 text-sm font-medium text-white">Upload</button></div>
            @if ($errors->any())
                <p class="text-sm text-red-700 md:col-span-2 xl:col-span-6">{{ $errors->first() }}</p>
            @endif
        </form>
        @if ($errors->any())
            <script>document.getElementById('upload-document')?.classList.remove('hidden')</script>
        @endif

        <form method="GET" action="{{ route('homeowner.documents.index') }}" class="mt-6 grid gap-3 rounded-[1.4rem] border border-[#ece7dc] bg-white p-4 shadow-sm md:grid-cols-2 xl:grid-cols-4">
            <label class="block text-sm">
                <span class="mb-1 block text-xs text-[#66756C]">Project</span>
                <select name="project" class="w-full rounded-2xl border border-[#ece7dc] bg-[#F7F4EE] px-3 py-2 text-sm" onchange="this.form.requestSubmit()">
                    <option value="">All Projects</option>
                    @foreach ($projects as $option)
                        <option value="{{ $option->id }}" @selected((int) ($filters['project'] ?? 0) === $option->id)>{{ $option->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block text-sm">
                <span class="mb-1 block text-xs text-[#66756C]">Type</span>
                <select name="type" class="w-full rounded-2xl border border-[#ece7dc] bg-[#F7F4EE] px-3 py-2 text-sm" onchange="this.form.requestSubmit()">
                    <option value="">All Types</option>
                    @foreach (\App\Models\Document::categories() as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['type'] ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block text-sm md:col-span-2">
                <span class="mb-1 block text-xs text-[#66756C]">Search</span>
                <span class="flex gap-2">
                    <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search documents..." class="w-full rounded-2xl border border-[#ece7dc] bg-[#F7F4EE] px-3 py-2 text-sm">
                    <button class="shrink-0 rounded-full bg-[#123D2B] px-4 py-2 text-sm font-medium text-white">Search</button>
                </span>
            </label>
        </form>
    @endif

    @php
        $noDocumentsAtAll = ! $project && $summary && (int) $summary->total === 0;
    @endphp

    @if ($documents->isEmpty())
        <div class="mt-6">
            @if ($project)
                @include('homeowner.partials.empty', ['title' => 'No documents found for this project.', 'body' => 'Upload a contract, drawing or receipt for this project.'])
            @elseif ($noDocumentsAtAll)
                @include('homeowner.partials.empty', ['title' => 'No documents yet', 'body' => 'Your project documents will appear here.'])
            @elseif (! empty($filters['project']))
                @include('homeowner.partials.empty', ['title' => 'No documents found for this project.', 'body' => 'Try another project, or clear the type and search filters.'])
            @else
                @include('homeowner.partials.empty', ['title' => 'No documents match these filters.', 'body' => 'Adjust the project, type or search and try again.'])
            @endif
        </div>
    @else
        <div class="mt-6 space-y-3 md:hidden">
            @foreach ($documents as $document)
                <article class="rounded-2xl border border-[#ece7dc] bg-white p-4 shadow-sm">
                    <p class="font-medium text-[#123D2B]">{{ $document->name }}</p>
                    @unless ($project)
                        <div class="mt-3">@include('homeowner.partials.project-chip', ['project' => $document->project])</div>
                    @endunless
                    <p class="mt-2 text-xs text-[#66756C]">{{ $document->categoryLabel() }} · {{ $document->created_at->format('j M Y') }} · {{ $document->sizeLabel() }}</p>
                    @if ($document->description)
                        <p class="mt-2 text-sm text-[#66756C]">{{ $document->description }}</p>
                    @endif
                    <p class="mt-3 flex gap-4 text-sm">
                        <a href="{{ route('homeowner.projects.documents.show', [$document->project, $document]) }}" class="font-medium text-[#123D2B]" target="_blank" rel="noopener">View</a>
                        <a href="{{ route('homeowner.projects.documents.download', [$document->project, $document]) }}" class="font-medium text-[#123D2B]">Download</a>
                    </p>
                </article>
            @endforeach
        </div>
        <div class="mt-6 hidden overflow-x-auto rounded-[1.4rem] border border-[#ece7dc] bg-white shadow-sm md:block">
            <table class="min-w-[72rem] w-full text-left text-sm">
                <thead class="bg-[#F6F1E7] text-[11px] uppercase tracking-[0.12em] text-[#66756C]">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">Document</th>
                        @unless ($project)<th class="px-4 py-3">Project</th>@endunless
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Uploaded By</th>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Size</th>
                        <th class="px-4 py-3">Description</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($documents as $document)
                        @php
                            $uploader = $document->uploader?->professionalProfile?->business_name ?: ($document->uploader?->name ?? 'RenovaHub');
                        @endphp
                        <tr class="border-t border-[#ece7dc] align-top">
                            <td class="px-4 py-3 text-[#66756C]">{{ $documents->firstItem() + $loop->index }}</td>
                            <td class="px-4 py-3 font-medium text-[#123D2B]">{{ $document->name }}</td>
                            @unless ($project)
                                <td class="px-4 py-3">@include('homeowner.partials.project-chip', ['project' => $document->project])</td>
                            @endunless
                            <td class="px-4 py-3">{{ $document->categoryLabel() }}</td>
                            <td class="px-4 py-3">{{ $uploader }}</td>
                            <td class="px-4 py-3">{{ $document->created_at->format('j M Y') }}</td>
                            <td class="px-4 py-3">{{ $document->sizeLabel() }}</td>
                            <td class="max-w-xs px-4 py-3 text-[#66756C]">{{ \Illuminate\Support\Str::limit($document->description, 100) ?: '—' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <a href="{{ route('homeowner.projects.documents.show', [$document->project, $document]) }}" class="font-medium text-[#123D2B] hover:underline" target="_blank" rel="noopener">View</a>
                                <a href="{{ route('homeowner.projects.documents.download', [$document->project, $document]) }}" class="ml-3 font-medium text-[#123D2B] hover:underline">Download</a>
                                @if ($project)
                                    <form method="POST" action="{{ route('homeowner.projects.documents.destroy', [$document->project, $document]) }}" class="inline" onsubmit="return confirm('Delete this document?')">
                                        @csrf @method('DELETE')
                                        <button class="ml-3 text-red-700 hover:underline">Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $documents->links() }}</div>
    @endif
</x-homeowner-layout>
