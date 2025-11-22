<?php

namespace App\Models;

use Filament\Panel\Concerns\HasFont;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyReport extends Model
{
    use HasFactory;
    // use LogsActivity;

    protected $table = 'monthly_reports';
    protected $fillable = [
        'user_id', 
        'month', 
        'year', 
        'report_date', 
        'content', 
        'total_days_reported', 
        'total_tasks', 
        'total_tickets', 
        'daily_report_ids', 
        'verified_by', 
        'verified_at', 
        'status'
    ];

    protected $casts = [
        'report_date' => 'date',
        'verified_at' => 'datetime',
        'daily_report_ids' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function relatedDailyReports()
    {
        if (empty($this->daily_report_ids)) {
            return collect();
        }

        return DailyReport::whereIn('id', $this->daily_report_ids)->get();
    }

    public function scopeForPeriod($query, $month, $year)
    {
        return $query->where('month', $month)->where('year', $year);
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'Verified');
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['Draft', 'Pending Verification']);
    }

    // public function getActivitylogOptions(): LogOptions
    // {
    //     return LogOptions::defaults()
    //         ->logAll()
    //         ->logOnlyDirty()
    //         ->useLogName('report_monthly');
    // }
}
