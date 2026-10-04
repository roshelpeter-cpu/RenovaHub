<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConversationAttachment extends Model
{
    protected $fillable = ['conversation_message_id', 'path', 'original_name'];

    public function message(): BelongsTo
    {
        return $this->belongsTo(ConversationMessage::class, 'conversation_message_id');
    }

    public function url(): string
    {
        return str_starts_with($this->path, 'images/') ? asset($this->path) : asset('storage/'.$this->path);
    }
}
