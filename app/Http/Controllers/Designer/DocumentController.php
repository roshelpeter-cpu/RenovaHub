<?php

namespace App\Http\Controllers\Designer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Designer\StoreDesignDocumentRequest;
use App\Models\Document;
use App\Models\Project;
use App\Services\DesignerWorkspaceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function index(Request $request, DesignerWorkspaceService $workspace): View
    {
        return view('designer.documents.index', $this->listing($request, $workspace, null));
    }

    public function project(Request $request, Project $project, DesignerWorkspaceService $workspace): View
    {
        $project = $workspace->findAccepted($request->user(), $project);

        return view('designer.documents.project', $this->listing($request, $workspace, $project));
    }

    public function store(StoreDesignDocumentRequest $request): RedirectResponse
    {
        $project = Project::query()->findOrFail($request->integer('project_id'));
        $this->authorize('uploadDesign', [Document::class, $project]);

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

        return back()->with('status', 'Design document uploaded.');
    }

    public function show(Request $request, Document $document): StreamedResponse
    {
        $this->authorize('view', $document);

        return Storage::disk($document->disk)->response($document->path, $document->original_name);
    }

    public function download(Request $request, Document $document): StreamedResponse
    {
        $this->authorize('view', $document);

        return Storage::disk($document->disk)->download($document->path, $document->original_name);
    }

    public function destroy(Request $request, Document $document): RedirectResponse
    {
        $this->authorize('delete', $document);
        Storage::disk($document->disk)->delete($document->path);
        $document->delete();

        return back()->with('status', 'Document deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function listing(Request $request, DesignerWorkspaceService $workspace, ?Project $project): array
    {
        $projects = $workspace->cards($request->user());
        $category = $request->string('category')->toString();
        $projectId = $project?->id ?: $request->integer('project');

        $documents = Document::query()
            ->whereIn('category', Document::designerCategories())
            ->when($project, fn ($query) => $query->where('project_id', $project->id))
            ->when(! $project, fn ($query) => $query->whereIn('project_id', $projects->pluck('id')))
            ->when($projectId && ! $project, fn ($query) => $query->where('project_id', $projectId))
            ->when($category !== '' && in_array($category, Document::designerCategories(), true), fn ($query) => $query->where('category', $category))
            ->with(['project', 'uploader'])
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return compact('documents', 'projects', 'project', 'category');
    }
}
