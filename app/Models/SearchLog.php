<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'keyword',
        'user_id',
        'session_id',
    ];
    
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}

