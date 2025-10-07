<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Ticket;

class TicketLocation extends Model
{
    use HasFactory;
    protected $table = 'ticket_locations';
    protected $fillable = [
        'name', 
        'is_active'
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'location_id');
    }
}
