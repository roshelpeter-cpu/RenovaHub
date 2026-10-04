<?php

namespace App\Policies;

use App\Models\Document;
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

    public function delete(User $user, Document $document): bool
    {
        return $this->view($user, $document);
    }
}
