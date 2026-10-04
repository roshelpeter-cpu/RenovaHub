<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MoodBoardItem extends Model
{
    protected $fillable = ['mood_board_id', 'kind', 'title', 'body', 'image', 'colour'];

    public function moodBoard(): BelongsTo
    {
        return $this->belongsTo(MoodBoard::class);
    }

    public function imageUrl(): ?string
    {
        if ($this->image === null) {
            return null;
        }

        return str_starts_with($this->image, 'images/')
            ? asset($this->image)
            : asset('storage/'.$this->image);
    }
}
