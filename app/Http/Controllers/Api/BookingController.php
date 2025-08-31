<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{

    public function getListBookingsUser(Request $request)
    {
        $validated = $request->validate(
            [
                'email' => 'required|email|exists:users,email',
                'start_date' => 'nullable|date_format:Y-m-d',
                'payment_method' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
                'status' => 'nullable|in:pending,cancelled,completed,confirmed',
                'payment_status' => 'nullable|in:pending,paid,cancelled',
                'payment_method' => 'nullable|in:cash,balance,bank_transfer',
            ]
        );

        $limit = $request->input('per_page', 12);

        $user = User::where('email', $validated['email'])->first();
    
        $query = $user->bookings()->orderBy('id','DESC');

        if (isset($validated['start_date']) &&  $validated['start_date']) {
            $query->whereDate('start_time', '>=', now()->createFromFormat('Y-m-d', $validated['start_date'])->startOfDay());
        }
        if (isset($validated['end_date']) && $validated['end_date']) {
            $query->whereDate('end_time', '<=', now()->createFromFormat('Y-m-d', $validated['end_date'])->endOfDay());
        }
        if (isset($validated['status']) && $validated['status']) {
            $query->status($validated['status']);
        }
        if (isset($validated['payment_status']) && $validated['payment_status']) {
            $query->paymentStatus($validated['payment_status']);
        }
        if (isset($validated['payment_method']) && $validated['payment_method']) {
            $query->paymentMethod($validated['payment_method']);
        }
        $data = $query->with('room')->get();

        return response()->json(['status' => true,  'message' => "Get data successfully", 'data' => $data]);
    }
    
    public function getDetailBookings(Booking $booking)
    {
        if (!$booking->id) {
            return response()->json(['status' => false,  'message' => "Booking not founds"], 422);
        }
        $booking->load(['room', 'user']);
        return response()->json(['status' => true, 'message' => 'Get detail booking successfully','data' => $booking ]);
    }
    
    public function getListBookings(Request $request) {
        $per_page = $request->input('per_page',20);
        $query = Booking::with(['room','user'])->orderBy('id','desc');
        $data = $query->paginate($per_page);
        
        return response()->json(['data' => $data,'message' => __('Get list bookings successfully'),'status' => true]);
    }
    
     public function changeStatus(Booking $booking,Request $request) {
        $validated = $request->validate(
            [
                'status' => 'required|in:pending,cancelled,completed,confirmed',
            ]
        );
        
        $booking->status = $validated['status'];
        $booking->save();
        
        $booking->load(['user','room']);
        return response()->json(['data' => $booking,'message' => __('Update status bookings successfully'),'status' => true]);
      
    }
    
     public function changePaymentStatus(Booking $booking,Request $request) {
        $validated = $request->validate(
            [
                 'payment_status' => 'required|in:pending,paid,cancelled',
            ]
        );
        
        $booking->payment_status = $validated['payment_status'];
        $booking->save();
        
        $booking->load(['user','room']);
        return response()->json(['data' => $booking,'message' => __('Update payment status bookings successfully'),'status' => true]);
      
    }
    
    public function getStatusBadge(Request $request) {
        $validated = $request->validate(
            [
                'type' => 'nullable|in:pending,cancelled,completed,confirmed',
            ]
        );
        
        if($validated && $validated['type']){
            
         $countData = Booking::status($validated['type'])->count();
         return response()->json(['data' => ['count' => $countData],'message' => __('Get badge count status completed bookings successfully'),'status' => true]);
        }
    }
    
    public function rooms(Request $request){
        $query = $request->get('query', '');
        
        $roomsQuery = Room::where('status', 'active');
        
        if (!empty($query)) {
            $roomsQuery->where('name', 'LIKE', '%' . $query . '%');
        }
        
        $featuredRooms = $roomsQuery->get();
        
        // Transform data để thêm full URL cho image và redirect URL
        $featuredRooms = $featuredRooms->map(function($room) {
            // Thêm app URL vào image_url
            if ($room->image_url) {
                $room->image_url = url($room->image_url);
            }
            
            // Thêm redirect URL
            $room->redirect_url = "https://book.hoi.com.vn/rooms/{$room->id}";
            
            return $room;
        });
        
        return response()->json([
            'status' => true,
            'data' => $featuredRooms
        ]);
    }
}
