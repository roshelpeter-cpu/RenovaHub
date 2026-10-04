<x-homeowner-layout :title="$project ? $project->name.' documents' : 'My Documents'" :canvas="true" :flush="(bool) $project">
    @if ($project)
        <x-project-context :project="$project" section="documents">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="font-serif text-3xl text-[#123D2B]">Project Documents</h2>
                <p class="mt-2 text-sm text-[#66756C]">Documents for this project only.</p>
            </div>
        </div>
        @if (! $project->isClosedRecord())
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
        @endif
        @include('homeowner.documents.partials.list')
        </x-project-context>
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
            @if ($projects->contains(fn ($option) => ! $option->isClosedRecord()))
                <button type="button" class="rounded-full bg-[#123D2B] px-4 py-2 text-sm font-medium text-white" onclick="document.getElementById('upload-document').classList.toggle('hidden')">+ Upload Document</button>
            @endif
        </div>

        <form id="upload-document" method="POST" action="{{ route('homeowner.documents.store') }}" enctype="multipart/form-data" class="mt-6 hidden grid gap-3 rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm md:grid-cols-2 xl:grid-cols-6">
            @csrf
            <label class="block text-sm"><span class="mb-1 block text-xs text-[#66756C]">Project</span>
                <select name="project_id" required class="w-full rounded-2xl border border-[#ece7dc] px-3 py-2 text-sm">
                    <option value="">Select a project</option>
                    @foreach ($projects as $option)
                        @continue($option->isClosedRecord())
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
        @include('homeowner.documents.partials.list')
    @endif
</x-homeowner-layout>
