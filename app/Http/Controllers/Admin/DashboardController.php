<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        //Toltal User
        $totalUsers = User::count();
        //Toltal Room
        $totalRooms = Room::count();
        //Toltal Booking
        $totalBookings = Booking::count();
        
        $totalPaidSuccess = Booking::where('payment_status','paid')->count();
        return view('admin.dashboard',compact('totalUsers', 'totalRooms', 'totalBookings','totalPaidSuccess'));
    }

    public function test()
    {
        return view('admin.test');
    }
}
