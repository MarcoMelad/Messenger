<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recipients extends Pivot
{
    use HasFactory, SoftDeletes;
    public $timestamps = false;
    protected $casts = [
        'read_at' => 'datetime'
    ];
    public function conversations(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
