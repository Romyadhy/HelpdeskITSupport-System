<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Ticket;

class TicketCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 
        'is_active'
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'category_id');
    }


}
