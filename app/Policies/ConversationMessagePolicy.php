<?php

namespace App\Policies;

use App\Models\ConversationMessage;
use App\Models\User;

class ConversationMessagePolicy
{
    /**
     * Deletion is limited to the sender. Another participant can read the
     * thread, but cannot remove a message they did not write.
     */
    public function delete(User $user, ConversationMessage $message): bool
    {
        return (int) $message->sender_id === (int) $user->id
            && $user->can('view', $message->conversation);
    }
}
