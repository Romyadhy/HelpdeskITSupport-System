<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\TicketCategory;
use App\Models\TicketLocation;
use Carbon\CarbonInterval;
use Carbon\Interval;

class Ticket extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'description', 'status', 'priority', 'user_id', 'solution', 'solved_by', 'started_at', 'solved_at', 'assigned_to', 'duration', 'category_id', 'location_id', 'is_escalation', 'escalated_at'];

    protected $casts = [
        'started_at' => 'datetime',
        'solved_at' => 'datetime',
        'escalated_at' => 'datetime',
        'is_escalation' => 'boolean',
    ];

    public function getDurationHumanAttribute(): string
    {
        if (is_null($this->duration) || $this->duration < 0) {
            return '-';
        }
        $ci = CarbonInterval::minutes($this->duration)->cascade();
        $parts = [];
        if ($ci->d) {
            $parts[] = $ci->d . 'd';
        }
        if ($ci->h) {
            $parts[] = $ci->h . 'h';
        }
        $parts[] = ($ci->i ?: 0) . 'm';
        return implode(' ', $parts);
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
}
