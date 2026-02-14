<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlineBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'preferred_date',
        'preferred_time',
        'service',
        'message',
        'status',
        'source',
        'meta',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'meta' => 'array',
    ];
}
