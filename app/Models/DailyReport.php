<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReport extends Model
{
    use HasFactory;
    protected $table = 'daily_reports';
    protected $fillable = ['user_id', 'report_date', 'content', 'verified_by', 'verified_at'];
    protected $casts = [
        'report_date' => 'date',
        'verified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'daily_report_tasks');
    }

    public function tickets()
    {
        return $this->belongsToMany(Ticket::class, 'daily_report_tickets');
    }
}
