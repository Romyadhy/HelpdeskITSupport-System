<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskCompletion extends Model
{
    use HasFactory;
    // protected $table = 'task_completions';
    protected $fillable = [
        'task_id',
        'user_id',
        'complated_at',
        'notes',
    ];

    protected $casts = [
        'complated_at' => 'datetime',
    ];

    public function task() :BelongsTo { 
        return $this->belongsTo(Task::class);
    }

    public function user() :BelongsTo  {
        return $this->belongsTo(User::class);
    }
}
