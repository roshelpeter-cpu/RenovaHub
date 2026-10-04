<x-homeowner-layout title="New Change Request">
    <h1 class="font-serif text-3xl text-forest">New change request</h1>
    <p class="mt-2 text-sm text-mist">{{ $project->name }}</p>
    <form method="POST" action="{{ route('homeowner.projects.change-requests.store', $project) }}" enctype="multipart/form-data" class="mt-6 max-w-2xl space-y-4 rounded-3xl border border-[#ece7dc] bg-white p-5 shadow-sm">
        @csrf
        <x-validation-errors />
        <div><label for="title" class="mb-1 block text-sm">Title</label><input id="title" name="title" required maxlength="150" value="{{ old('title') }}" class="w-full rounded-2xl border border-line px-3 py-2 text-sm"></div>
        <div><label for="description" class="mb-1 block text-sm">Description</label><textarea id="description" name="description" required rows="4" class="w-full rounded-2xl border border-line px-3 py-2 text-sm">{{ old('description') }}</textarea></div>
        <div><label for="reason" class="mb-1 block text-sm">Reason</label><textarea id="reason" name="reason" rows="3" class="w-full rounded-2xl border border-line px-3 py-2 text-sm">{{ old('reason') }}</textarea></div>
        <div class="grid gap-3 sm:grid-cols-2">
            <div><label for="category" class="mb-1 block text-sm">Category</label>
                <select id="category" name="category" class="w-full rounded-2xl border border-line px-3 py-2 text-sm">
                    @foreach (\App\Models\ChangeRequest::categories() as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
                </select>
            </div>
            <div><label for="priority" class="mb-1 block text-sm">Priority</label>
                <select id="priority" name="priority" class="w-full rounded-2xl border border-line px-3 py-2 text-sm">
                    <option value="low">Low</option><option value="normal" selected>Normal</option><option value="high">High</option>
                </select>
            </div>
        </div>
        <div><label for="attachment" class="mb-1 block text-sm">Attachment</label><input id="attachment" name="attachment" type="file" accept=".jpg,.jpeg,.png,.webp,.pdf" class="text-sm"></div>
        <button class="rounded-full bg-forest px-5 py-3 text-sm font-medium text-ivory">Submit change request</button>
    </form>
</x-homeowner-layout>
