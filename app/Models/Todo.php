<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Todo extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = ['description', 'completed', 'priority', 'due_date'];

    protected $attributes = [
        'completed' => false,
        'priority' => false,
    ];

    // belongs to user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
