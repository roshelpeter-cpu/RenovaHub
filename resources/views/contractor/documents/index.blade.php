<x-contractor-layout title="Documents">
    <h1 class="rh-serif text-4xl text-[#123D2B]">Documents</h1>
    <p class="mt-2 text-sm text-[#66756C]">Construction files stay on the private disk and open only through an authorised download.</p>
    <form method="GET" class="mt-4 flex flex-wrap gap-2">
        <select name="project" class="rounded-full border border-[#ddd6c8] bg-white px-3 py-2 text-sm" onchange="this.form.requestSubmit()">
            <option value="">All projects</option>
            @foreach ($projects as $project)
                <option value="{{ $project->id }}" @selected($projectId === $project->id)>{{ $project->name }}</option>
            @endforeach
        </select>
        <select name="category" class="rounded-full border border-[#ddd6c8] bg-white px-3 py-2 text-sm" onchange="this.form.requestSubmit()">
            <option value="">All categories</option>
            @foreach ($categories as $value => $label)
                <option value="{{ $value }}" @selected($category === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </form>
    <ul class="mt-6 space-y-3">
        @forelse ($documents as $document)
            <li class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-[#ece7dc] bg-white px-4 py-3 text-sm">
                <div>
                    <p class="font-medium text-[#123D2B]">{{ $document->name }}</p>
                    <p class="text-[#66756C]">{{ $document->project?->name }} · {{ $document->categoryLabel() }} · {{ $document->sizeLabel() }}</p>
                </div>
                <a href="{{ route('contractor.documents.download', $document) }}" class="text-[#123D2B] underline">Download</a>
            </li>
        @empty
            <li class="text-sm text-[#66756C]">No documents yet.</li>
        @endforelse
    </ul>
    <form method="POST" action="{{ route('contractor.documents.store') }}" enctype="multipart/form-data" class="mt-6 grid gap-3 rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm md:grid-cols-2">
        @csrf
        <h2 class="rh-serif text-2xl text-[#123D2B] md:col-span-2">Upload</h2>
        <select name="project_id" class="rounded-2xl border border-[#ddd6c8] px-3 py-2 text-sm" required>
            @foreach ($projects as $project)
                <option value="{{ $project->id }}">{{ $project->name }}</option>
            @endforeach
        </select>
        <input name="name" placeholder="Document name" class="rounded-2xl border border-[#ddd6c8] px-3 py-2 text-sm" required>
        <select name="category" class="rounded-2xl border border-[#ddd6c8] px-3 py-2 text-sm">
            @foreach ($categories as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
        <input type="file" name="file" class="text-sm" required>
        <textarea name="description" rows="2" placeholder="Description" class="rounded-2xl border border-[#ddd6c8] px-3 py-2 text-sm md:col-span-2"></textarea>
        <button class="w-fit rounded-full bg-[#123D2B] px-4 py-2 text-sm text-white">Upload document</button>
    </form>
</x-contractor-layout>
