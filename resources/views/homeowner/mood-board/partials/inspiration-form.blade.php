@php
    $openProjects = $projects->filter(fn ($option) => ! $option->isClosedRecord());
@endphp
@if ((($project ?? null) === null && $openProjects->isNotEmpty()) || (($project ?? null) && ! $project->isClosedRecord()))
    <form method="POST" action="{{ isset($project) && $project ? route('homeowner.projects.mood-board.items.store', $project) : route('homeowner.mood-board.store') }}" enctype="multipart/form-data" class="mt-4 grid gap-3 rounded-[1.4rem] border border-[#ece7dc] bg-white p-4 shadow-sm md:grid-cols-2 xl:grid-cols-6">
        @csrf
        @if (! isset($project) || ! $project)
            <label class="block text-sm">
                <span class="mb-1 block text-xs text-[#66756C]">Project</span>
                <select name="project_id" required class="w-full rounded-2xl border border-[#ece7dc] px-3 py-2 text-sm">
                    @foreach ($openProjects as $option)
                        <option value="{{ $option->id }}" @selected((int) old('project_id', $selected ?? 0) === $option->id)>{{ $option->name }}</option>
                    @endforeach
                </select>
            </label>
        @endif
        <label class="block text-sm">
            <span class="mb-1 block text-xs text-[#66756C]">Type</span>
            <select name="kind" class="w-full rounded-2xl border border-[#ece7dc] px-3 py-2 text-sm">
                @foreach (['inspiration' => 'Inspiration image', 'furniture' => 'Furniture', 'material' => 'Material', 'colour' => 'Colour', 'note' => 'Design note'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('kind') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <label class="block text-sm"><span class="mb-1 block text-xs text-[#66756C]">Title</span><input name="title" value="{{ old('title') }}" required maxlength="120" class="w-full rounded-2xl border border-[#ece7dc] px-3 py-2 text-sm"></label>
        <label class="block text-sm"><span class="mb-1 block text-xs text-[#66756C]">Note</span><input name="body" value="{{ old('body') }}" maxlength="1000" class="w-full rounded-2xl border border-[#ece7dc] px-3 py-2 text-sm"></label>
        <label class="block text-sm"><span class="mb-1 block text-xs text-[#66756C]">Colour</span><input name="colour" value="{{ old('colour') }}" placeholder="#F8F6F1" class="w-full rounded-2xl border border-[#ece7dc] px-3 py-2 text-sm"></label>
        <label class="block text-sm"><span class="mb-1 block text-xs text-[#66756C]">Image</span><input name="image" type="file" accept=".jpg,.jpeg,.png,.webp" class="w-full text-sm"></label>
        <div class="flex items-end md:col-span-2 xl:col-span-6"><button class="rounded-full bg-[#123D2B] px-4 py-2 text-sm font-medium text-white">Save inspiration</button></div>
        @if ($errors->any())
            <p class="text-sm text-red-700 md:col-span-2 xl:col-span-6">{{ $errors->first() }}</p>
        @endif
    </form>
@endif
