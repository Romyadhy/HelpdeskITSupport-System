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
        'location_id',
        'location_name',
        'category_id',
        'category_name',
        'created_by',
        'created_by_name',
        'ticket_created_at',
        'ticket_started_at',
        'ticket_solved_at',
        'waiting_duration',
        'progress_duration',
        'total_duration',

    ];


    public function dailyReport()
    {
        return $this->belongsTo(DailyReport::class);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
