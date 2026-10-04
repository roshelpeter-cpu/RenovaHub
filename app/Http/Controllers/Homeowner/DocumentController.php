<?php

namespace App\Http\Controllers\Homeowner;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentRequest;
use App\Models\Document;
use App\Models\Project;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Project::class);

        $category = $request->string('category')->toString();
        $search = trim($request->string('search')->toString());

        $documents = Document::query()
            ->whereIn('project_id', $request->user()->projects()->select('id'))
            ->with(['project', 'uploader'])
            ->when($category !== '', fn ($query) => $query->where('category', $category))
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'like', '%'.addcslashes($search, '%_\\').'%');
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('homeowner.documents.index', [
            'documents' => $documents,
            'project' => null,
            'category' => $category,
            'search' => $search,
        ]);
    }

    public function project(Project $project): View
    {
        Gate::authorize('view', $project);

        return view('homeowner.documents.index', [
            'documents' => $project->documents()->with('uploader')->latest()->paginate(12),
            'project' => $project,
            'category' => '',
            'search' => '',
        ]);
    }

    public function store(StoreDocumentRequest $request, Project $project, ActivityLogService $activity): RedirectResponse
    {
        $file = $request->file('file');
        $path = $file->store('projects/'.$project->id.'/documents', 'local');

        $project->documents()->create([
            'uploaded_by' => $request->user()->id,
            'name' => $request->string('name')->toString(),
            'original_name' => $file->getClientOriginalName(),
            'category' => $request->string('category')->toString(),
            'disk' => 'local',
            'path' => $path,
            'mime' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ]);

        $activity->record($project, $request->user(), 'document.uploaded', 'A document was uploaded.');

        return back()->with('status', 'Document uploaded.');
    }

    public function download(Project $project, Document $document): StreamedResponse
    {
        Gate::authorize('view', $document);
        abort_unless($document->project_id === $project->id, 404);

        return Storage::disk($document->disk)->download($document->path, $document->original_name);
    }

    public function destroy(Project $project, Document $document): RedirectResponse
    {
        Gate::authorize('delete', $document);
        abort_unless($document->project_id === $project->id, 404);

        Storage::disk($document->disk)->delete($document->path);
        $document->delete();

        return back()->with('status', 'Document deleted.');
    }
}
