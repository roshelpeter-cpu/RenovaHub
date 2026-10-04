<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DesignFeedback extends Model
{
    protected $table = 'design_feedback';

    protected $fillable = ['mood_board_id', 'user_id', 'title', 'comment', 'attachment'];

    public function moodBoard(): BelongsTo
    {
        return $this->belongsTo(MoodBoard::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
