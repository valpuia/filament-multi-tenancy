<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function priority(): BelongsTo
    {
        return $this->belongsTo(Priority::class);
    }
}
