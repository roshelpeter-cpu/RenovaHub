@php
    $noDocumentsAtAll = ! $project && $summary && (int) $summary->total === 0;
@endphp

@if ($documents->isEmpty())
    <div class="mt-6">
        @if ($project)
            @include('homeowner.partials.empty', ['title' => 'No documents found for this project.', 'body' => $project->isClosedRecord() ? 'Documents from this completed project stay here as a record.' : 'Upload a contract, drawing or receipt for this project.'])
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
                            @can('delete', $document)
                                <form method="POST" action="{{ route('homeowner.projects.documents.destroy', [$document->project, $document]) }}" class="inline" onsubmit="return confirm('Delete this document?')">
                                    @csrf @method('DELETE')
                                    <button class="ml-3 text-red-700 hover:underline">Delete</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $documents->links() }}</div>
@endif
