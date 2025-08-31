<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingLog extends Model
{
    use HasFactory;
        protected $fillable = [
        'booking_id',
        'user_id', 
        'field_name',
        'old_value',
        'new_value',
        'action_type',
        'ip_address'
    ];

}
