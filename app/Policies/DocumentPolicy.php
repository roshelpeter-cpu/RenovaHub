<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\Project;
use App\Models\User;

class DocumentPolicy
{
    /**
     * A document is visible only through the project that owns it.
     */
    public function view(User $user, Document $document): bool
    {
        return $user->can('view', $document->project);
    }

    /**
     * Uploads and deletions follow the project. A completed project can
     * still be read, but it cannot receive a new file.
     */
    public function create(User $user, Project $project): bool
    {
        return $user->can('contribute', $project);
    }

    public function delete(User $user, Document $document): bool
    {
        return $user->can('contribute', $document->project);
    }
}
