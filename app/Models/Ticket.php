<?php

namespace App\Models;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;
    // use LogsActivity;

    protected $fillable = ['title', 'description', 'status', 'priority', 'user_id', 'solution', 'solved_by', 'started_at', 'solved_at', 'assigned_to', 'duration', 'category_id', 'location_id', 'is_escalation', 'escalated_at'];

    protected $casts = [
        'started_at' => 'datetime',
        'solved_at' => 'datetime',
        'escalated_at' => 'datetime',
        'is_escalation' => 'boolean',
    ];

    // public function getDurationHumanAttribute(): string
    // {
    //     if (is_null($this->duration) || $this->duration < 0) {
    //         return '-';
    //     }
    //     $ci = CarbonInterval::minutes($this->duration)->cascade();
    //     $parts = [];
    //     if ($ci->d) {
    //         $parts[] = $ci->d . 'd';
    //     }
    //     if ($ci->h) {
    //         $parts[] = $ci->h . 'h';
    //     }
    //     $parts[] = ($ci->i ?: 0) . 'm';

    //     return implode(' ', $parts);
    // }

    //durasi ticket di buka dan di kerjakan
    public function getWaitingDurationHumanAttribute()
    {
        if (!$this->started_at) {
            return null;
        }

        $minutes = $this->created_at->diffInMinutes($this->started_at);
        $interval = CarbonInterval::minutes($minutes)->cascade();

        return ($interval->hours ? $interval->hours . 'h ' : '') . $interval->minutes . 'm';
    }
    //durasi ticket di kerjakan sampai selesai
    public function getProgressDurationHumanAttribute()
    {
        if (!$this->started_at) {
            return null;
        }

        $end = $this->solved_at ?? now();

        $minutes = $this->started_at->diffInMinutes($end);
        $interval = CarbonInterval::minutes($minutes)->cascade();

        return ($interval->hours ? $interval->hours . 'h ' : '') . $interval->minutes . 'm';
    }

    //durasi total ticket dari di buat sampai selesai
    public function getTotalDurationHumanAttribute()
    {
        if (!$this->solved_at) {
            return null;
        }

        $minutes = $this->created_at->diffInMinutes($this->solved_at);
        $interval = CarbonInterval::minutes($minutes)->cascade();

        return ($interval->hours ? $interval->hours . 'h ' : '') . $interval->minutes . 'm';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function solver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'solved_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(TicketLocation::class, 'location_id');
    }

    public function dailyReports()
    {
        return $this->belongsToMany(DailyReport::class, 'daily_report_tickets');
    }

    public function notes(){
        return $this->hasMany(TicketNote::class)->latest();
    }
}
