<?php

namespace App\Http\Controllers\Contractor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contractor\StoreDocumentRequest;
use App\Models\Document;
use App\Models\Project;
use App\Services\Contractor\ContractorWorkspaceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function index(Request $request, ContractorWorkspaceService $workspace): View
    {
        $projects = $workspace->accepted($request->user())->orderBy('name')->get();
        $projectId = $request->integer('project');
        $category = $request->string('category')->toString();

        if ($projectId > 0 && ! $projects->contains('id', $projectId)) {
            abort(404);
        }

        $documents = Document::query()
            ->whereIn('project_id', $projects->pluck('id'))
            ->when($projectId > 0, fn ($query) => $query->where('project_id', $projectId))
            ->when($category !== '', fn ($query) => $query->where('category', $category))
            ->with('project')
            ->latest()
            ->get();

        return view('contractor.documents.index', [
            'documents' => $documents,
            'projects' => $projects,
            'projectId' => $projectId,
            'category' => $category,
            'categories' => Document::categories(),
        ]);
    }

    public function store(StoreDocumentRequest $request): RedirectResponse
    {
        $project = Project::query()->findOrFail($request->integer('project_id'));
        $file = $request->file('file');

        $project->documents()->create([
            'uploaded_by' => $request->user()->id,
            'name' => $request->validated('name'),
            'description' => $request->validated('description'),
            'original_name' => $file->getClientOriginalName(),
            'category' => $request->validated('category'),
            'disk' => 'local',
            'path' => $file->store('projects/'.$project->id.'/documents', 'local'),
            'mime' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        return back()->with('status', 'Document stored privately for this project.');
    }

    public function show(Document $document): StreamedResponse
    {
        $this->authorize('view', $document);

        return Storage::disk($document->disk)->response($document->path, $document->original_name);
    }

    public function download(Document $document): StreamedResponse
    {
        $this->authorize('view', $document);

        return Storage::disk($document->disk)->download($document->path, $document->original_name);
    }

    public function destroy(Document $document): RedirectResponse
    {
        $this->authorize('delete', $document);
        Storage::disk($document->disk)->delete($document->path);
        $document->delete();

        return back()->with('status', 'Document deleted.');
    }
}
