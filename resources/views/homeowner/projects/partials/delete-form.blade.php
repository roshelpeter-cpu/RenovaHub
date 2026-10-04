<form method="POST" action="{{ route('homeowner.projects.destroy', $project) }}" onsubmit="return confirm('Are you sure you want to delete this project? This action cannot be undone.')">
    @csrf
    @method('DELETE')
    <button type="submit" class="{{ $class ?? 'rounded-full border border-red-200 px-4 py-2 text-sm font-medium text-red-700 transition hover:bg-red-50' }}">{{ $buttonLabel ?? 'Delete' }}</button>
</form>
