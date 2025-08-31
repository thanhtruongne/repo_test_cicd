<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller 
{
    public function show(Room $room)
    {
        // Lấy lịch đặt phòng
        $bookings = $room->bookings()
            ->where('status', '!=', 'cancelled')
            ->where('start_time', '>=', now())
            ->get()
            ->map(function($booking) {
                return [
                    'title' => 'Đã đặt',
                    'start' => $booking->start_time,
                    'end' => $booking->end_time
                ];
            });

        return view('rooms.show', compact('room', 'bookings'));
    }

    public function getBookings(Room $room)
    {
        $bookings = $room->bookings()
            ->where('status', '!=', 'cancelled')
            ->where('start_time', '>=', now())
            ->get()
            ->map(function($booking) {
                return [
                    'title' => 'Đã đặt',
                    'start' => $booking->start_time,
                    'end' => $booking->end_time
                ];
            });

        return response()->json($bookings);
    }
    public function index() {
        $featuredRooms = Room::where('status', 'active')
        ->get();
        return view('rooms.index', compact('featuredRooms'));
    }
}