<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TaskCompletion extends Model
{
    use HasFactory;
    use LogsActivity;
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

     public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            // supaya properties->attributes berisi kolom yang kita butuhkan saja
            ->logOnly([
                'task_id',
                'user_id',
                'complated_at',
                'notes',
            ])
            ->logOnlyDirty()
            ->useLogName('task_done')
            ->setDescriptionForEvent(function ($eventName) {
                return "Task completion {$eventName}";
            });
    }

}
