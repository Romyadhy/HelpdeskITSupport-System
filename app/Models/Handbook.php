<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Handbook extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category',
        'uploaded_by',
    ];


    public function uploader(){
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
