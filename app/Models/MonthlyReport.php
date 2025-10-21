<?php

namespace App\Models;

use Filament\Panel\Concerns\HasFont;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyReport extends Model
{
    use HasFactory;
    protected $table = 'monthly_reports';
    protected $fillable = [
        'user',
        'report_date',
        'content',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'report_date' => 'date',
        'verified_at' => 'datetime',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function verifier(){
        return $this->belongsTo(User::class, 'verified_at');
    }
}
