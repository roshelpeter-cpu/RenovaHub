<x-homeowner-layout title="Documents">
    @if ($project)
        @include('homeowner.projects.partials.tabs', ['project' => $project])
    @endif
    <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="font-serif text-3xl text-forest sm:text-4xl">Documents</h1>
            <p class="mt-2 text-sm text-mist">Contracts, drawings and receipts stay with the project.</p>
        </div>
    </div>
    @if ($project)
        <form method="POST" action="{{ route('homeowner.projects.documents.store', $project) }}" enctype="multipart/form-data" class="mt-6 grid gap-3 rounded-3xl border border-[#ece7dc] bg-white p-5 shadow-sm md:grid-cols-4">
            @csrf
            <div><label for="doc-name" class="mb-1 block text-sm">Name</label><input id="doc-name" name="name" required maxlength="150" class="w-full rounded-2xl border border-line px-3 py-2 text-sm"></div>
            <div><label for="doc-category" class="mb-1 block text-sm">Category</label>
                <select id="doc-category" name="category" class="w-full rounded-2xl border border-line px-3 py-2 text-sm">
                    @foreach (['contracts' => 'Contracts', 'design' => 'Design', 'floor_plans' => 'Floor Plans', 'invoices' => 'Invoices', 'receipts' => 'Receipts', 'materials' => 'Materials', 'construction' => 'Construction', 'other' => 'Other'] as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div><label for="doc-file" class="mb-1 block text-sm">File</label><input id="doc-file" name="file" type="file" required accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx" class="w-full text-sm"></div>
            <div class="flex items-end"><button type="submit" class="rounded-full bg-forest px-4 py-2 text-sm font-medium text-ivory">Upload Document</button></div>
            @error('file') <p class="text-sm text-red-700 md:col-span-4">{{ $message }}</p> @enderror
        </form>
    @else
        <form method="GET" class="mt-4 flex flex-wrap gap-2">
            <input name="search" value="{{ $search }}" placeholder="Search documents..." class="rounded-full border border-line bg-white px-4 py-2 text-sm">
            <button class="rounded-full bg-forest px-4 py-2 text-sm text-ivory">Search</button>
        </form>
    @endif
    @if ($documents->isEmpty())
        <div class="mt-6">@include('homeowner.partials.empty', ['title' => 'No documents uploaded', 'body' => 'Upload a contract, drawing or receipt from a project.'])</div>
    @else
        <div class="mt-6 overflow-x-auto rounded-3xl border border-[#ece7dc] bg-white shadow-sm">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-[#F6F1E7] text-xs uppercase tracking-[0.12em] text-mist"><tr><th class="px-4 py-3">Name</th><th class="px-4 py-3">Category</th><th class="px-4 py-3">Uploaded by</th><th class="px-4 py-3">Date</th><th class="px-4 py-3">Size</th><th class="px-4 py-3">Actions</th></tr></thead>
                <tbody>
                    @foreach ($documents as $document)
                        <tr class="border-t border-[#ece7dc]">
                            <td class="px-4 py-3 font-medium">{{ $document->name }}</td>
                            <td class="px-4 py-3">{{ str($document->category)->replace('_', ' ')->title() }}</td>
                            <td class="px-4 py-3">{{ $document->uploader?->name ?? 'RenovaHub' }}</td>
                            <td class="px-4 py-3">{{ $document->created_at->format('j M Y') }}</td>
                            <td class="px-4 py-3">{{ $document->sizeLabel() }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('homeowner.projects.documents.download', [$document->project, $document]) }}" class="text-forest hover:underline">Download</a>
                                <form method="POST" action="{{ route('homeowner.projects.documents.destroy', [$document->project, $document]) }}" class="inline" onsubmit="return confirm('Delete this document?')">
                                    @csrf @method('DELETE')
                                    <button class="ml-3 text-red-700 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $documents->links() }}</div>
    @endif
</x-homeowner-layout>
