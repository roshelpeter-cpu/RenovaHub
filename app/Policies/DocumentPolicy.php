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
        if ($user->can('view', $document->project)) {
            return true;
        }

        // Designers see design files on accepted projects, not contractor invoices or quotations.
        return $user->can('design', $document->project)
            && in_array($document->category, Document::designerCategories(), true);
    }

    /**
     * Uploads and deletions follow the project. A completed project can
     * still be read, but it cannot receive a new file.
     */
    public function create(User $user, Project $project): bool
    {
        return $user->can('contribute', $project);
    }

    public function uploadDesign(User $user, Project $project): bool
    {
        return $user->can('design', $project) && ! $project->isClosedRecord();
    }

    public function delete(User $user, Document $document): bool
    {
        if ($user->isDesigner()) {
            return $user->can('design', $document->project)
                && (int) $document->uploaded_by === (int) $user->id
                && in_array($document->category, Document::designerCategories(), true);
        }

        return $user->can('contribute', $document->project);
    }
}
