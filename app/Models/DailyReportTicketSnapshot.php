<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReportTicketSnapshot extends Model
{
    use HasFactory;

    protected $table = 'daily_report_ticket_snapshots';

    protected $fillable = [
        'daily_report_id',
        'ticket_id',
        'title',
        'description',
        'status',
        'priority',
        'solution',
        'solved_by',
        'solved_by_name',
    ];

    /**
     * Get the daily report this snapshot belongs to.
     */
    public function dailyReport()
    {
        return $this->belongsTo(DailyReport::class);
    }

    /**
     * Get the original ticket (for reference).
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
