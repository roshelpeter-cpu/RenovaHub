<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\StoreChangeRequestRequest;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ChangeRequestResource;
use App\Http\Resources\DocumentResource;
use App\Http\Resources\MessageResource;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\PaymentResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\QuotationResource;
use App\Http\Resources\TaskResource;
use App\Models\ChangeRequest;
use App\Models\Project;
use App\Models\Quotation;
use App\Services\ProjectService;
use App\Services\QuotationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProjectController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Project::class);

        $projects = $request->user()->projects()->latest()->paginate(10);

        return $this->page($projects, ProjectResource::class, 'Projects retrieved successfully.');
    }

    public function store(StoreProjectRequest $request, ProjectService $projects): JsonResponse
    {
        $project = $projects->createForHomeowner(
            $request->user(),
            $request->safe()->only(['name', 'description', 'requirements', 'additional_instructions', 'renovation_type', 'property_type']),
        );

        return $this->ok(new ProjectResource($project), 'Project created successfully.', 201);
    }

    public function show(Project $project): JsonResponse
    {
        Gate::authorize('view', $project);

        return $this->ok(new ProjectResource($project), 'Project retrieved successfully.');
    }

    public function update(UpdateProjectRequest $request, Project $project, ProjectService $projects): JsonResponse
    {
        $projects->updateDetails($project, $request->safe()->only(['name', 'description', 'requirements', 'additional_instructions', 'renovation_type', 'property_type']));

        return $this->ok(new ProjectResource($project->fresh()), 'Project updated successfully.');
    }

    public function destroy(Project $project, ProjectService $projects): JsonResponse
    {
        Gate::authorize('delete', $project);
        $projects->deleteProject($project);

        return $this->ok(null, 'Project deleted successfully.');
    }

    public function tasks(Project $project): JsonResponse
    {
        Gate::authorize('view', $project);

        return $this->page($project->tasks()->with('assignee')->latest()->paginate(10), TaskResource::class, 'Tasks retrieved successfully.');
    }

    public function documents(Project $project): JsonResponse
    {
        Gate::authorize('view', $project);

        return $this->page($project->documents()->latest()->paginate(10), DocumentResource::class, 'Documents retrieved successfully.');
    }

    public function quotations(Project $project): JsonResponse
    {
        Gate::authorize('view', $project);

        return $this->page($project->quotations()->latest()->paginate(10), QuotationResource::class, 'Quotations retrieved successfully.');
    }

    public function changeRequests(Project $project): JsonResponse
    {
        Gate::authorize('view', $project);

        return $this->page($project->changeRequests()->latest()->paginate(10), ChangeRequestResource::class, 'Change requests retrieved successfully.');
    }

    public function storeChangeRequest(StoreChangeRequestRequest $request, Project $project): JsonResponse
    {
        $change = $project->changeRequests()->create([
            'requested_by' => $request->user()->id,
            'title' => $request->string('title')->toString(),
            'description' => $request->string('description')->toString(),
            'reason' => $request->string('reason')->toString() ?: null,
            'category' => $request->string('category')->toString(),
            'priority' => $request->string('priority')->toString(),
            'status' => ChangeRequest::STATUS_SUBMITTED,
        ]);

        return $this->ok(new ChangeRequestResource($change), 'Change request submitted successfully.', 201);
    }

    public function messages(Project $project): JsonResponse
    {
        Gate::authorize('view', $project);

        return $this->page($project->messages()->with('sender')->latest()->paginate(20), MessageResource::class, 'Messages retrieved successfully.');
    }

    public function storeMessage(StoreMessageRequest $request, Project $project): JsonResponse
    {
        $message = $project->messages()->create([
            'sender_id' => $request->user()->id,
            'body' => $request->string('body')->toString(),
            'read_at' => now(),
        ]);
        $message->setRelation('sender', $request->user());

        return $this->ok(new MessageResource($message), 'Message sent successfully.', 201);
    }

    public function payments(Project $project): JsonResponse
    {
        Gate::authorize('view', $project);

        return $this->page($project->payments()->latest()->paginate(10), PaymentResource::class, 'Payments retrieved successfully.');
    }

    public function notifications(Request $request, Project $project): JsonResponse
    {
        Gate::authorize('view', $project);

        $notifications = $request->user()->notifications()->latest()->paginate(15);

        return $this->page($notifications, NotificationResource::class, 'Notifications retrieved successfully.');
    }

    public function approveQuotation(Project $project, Quotation $quotation, QuotationService $quotations): JsonResponse
    {
        return $this->decide($project, $quotation, $quotations, Quotation::STATUS_APPROVED, 'Quotation approved successfully.');
    }

    public function rejectQuotation(Project $project, Quotation $quotation, QuotationService $quotations): JsonResponse
    {
        return $this->decide($project, $quotation, $quotations, Quotation::STATUS_REJECTED, 'Quotation rejected successfully.');
    }

    private function decide(Project $project, Quotation $quotation, QuotationService $quotations, string $status, string $message): JsonResponse
    {
        Gate::authorize('update', $quotation);
        abort_unless($quotation->project_id === $project->id, 404);

        $quotations->decide($quotation, request()->user(), $status);

        return $this->ok(new QuotationResource($quotation->fresh()), $message);
    }

    private function page($paginator, string $resource, string $message): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $resource::collection(collect($paginator->items()))->resolve(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }
}
