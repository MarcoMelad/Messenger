<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Participants extends Pivot
{
    use HasFactory;

    public $timestamps = false;
    protected $casts = [
        'joined_at' => 'datetime'
    ];
    public function conversations(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
