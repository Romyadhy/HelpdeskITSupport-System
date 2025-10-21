<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;
    // protected $table = 'tasks';
    protected $fillable = [
        'title',
        'description',
        'frequency',
        'is_active',
        // 'taks_completions'
    ];

    public function completions(): HasMany
    {
        return $this->hasMany(TaskCompletion::class, 'task_id');
    }

    public function dailyReports()
    {
        return $this->belongsToMany(DailyReport::class, 'daily_report_tasks');
    }
}
