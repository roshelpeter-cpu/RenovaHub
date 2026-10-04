<?php

namespace App\Http\Controllers\Homeowner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Homeowner\DocumentIndexRequest;
use App\Http\Requests\Homeowner\StoreGlobalDocumentRequest;
use App\Http\Requests\StoreDocumentRequest;
use App\Models\Document;
use App\Models\Project;
use App\Services\ActivityLogService;
use App\Services\DocumentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function index(DocumentIndexRequest $request, DocumentService $documents): View
    {
        Gate::authorize('viewAny', Project::class);

        $filters = $documents->filters($request->user(), $request->validated());

        return view('homeowner.documents.index', [
            ...$documents->globalIndex($request->user(), $filters),
            'project' => null,
        ]);
    }

    public function project(Project $project): View
    {
        Gate::authorize('view', $project);

        return view('homeowner.documents.index', [
            'documents' => $project->documents()->with(['uploader.professionalProfile', 'project'])->latest()->paginate(12),
            'project' => $project,
            'projects' => collect(),
            'summary' => null,
            'filters' => ['project' => null, 'type' => '', 'search' => ''],
        ]);
    }

    public function store(StoreDocumentRequest $request, Project $project, DocumentService $documents, ActivityLogService $activity): RedirectResponse
    {
        $documents->storeUpload(
            $project,
            $request->user(),
            $request->file('file'),
            $request->string('name')->toString(),
            $request->string('category')->toString(),
            $request->string('description')->toString() ?: null,
        );

        $activity->record($project, $request->user(), 'document.uploaded', 'A document was uploaded.');

        return back()->with('status', 'Document uploaded.');
    }

    /**
     * Global upload still stores the file on the chosen owned project.
     * The project id is checked in the form request before this runs.
     */
    public function storeGlobal(StoreGlobalDocumentRequest $request, DocumentService $documents, ActivityLogService $activity): RedirectResponse
    {
        $project = $request->user()->projects()->findOrFail($request->integer('project_id'));

        $documents->storeUpload(
            $project,
            $request->user(),
            $request->file('file'),
            $request->string('name')->toString(),
            $request->string('category')->toString(),
            $request->string('description')->toString() ?: null,
        );

        $activity->record($project, $request->user(), 'document.uploaded', 'A document was uploaded.');

        return redirect()
            ->route('homeowner.documents.index', ['project' => $project->id])
            ->with('status', 'Document uploaded.');
    }

    public function show(Project $project, Document $document): StreamedResponse
    {
        $this->authorizeDocument($project, $document);

        return Storage::disk($document->disk)->response($document->path, $document->original_name);
    }

    public function download(Project $project, Document $document): StreamedResponse
    {
        $this->authorizeDocument($project, $document);

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

    /**
     * View and download both require the document policy and a matching project.
     * A swapped document id on another project returns 404 instead of the file.
     */
    private function authorizeDocument(Project $project, Document $document): void
    {
        Gate::authorize('view', $document);
        abort_unless($document->project_id === $project->id, 404);
        abort_unless(Storage::disk($document->disk)->exists($document->path), 404);
    }
}
