<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Booking extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'room_id', 
        'start_time',
        'end_time',
        'total_hours',
        'total_amount',
        'payment_method',
        'payment_status',
        'status',
        'notes',
        'transaction_id'
    ];
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
    
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    public function scopePaymentStatus($query, $method_status)
    {
        return $query->where('payment_status', $method_status);
    }
}
