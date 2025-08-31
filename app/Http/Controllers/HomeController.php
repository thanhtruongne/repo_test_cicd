<?php 
namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Booking;
use Illuminate\Http\Request;
class HomeController extends Controller
{
    public function index()
    {
        $featuredRooms = Room::where('status', 'active')
            ->take(6)
            ->get();

        $totalBookings = Booking::count();
        $totalCustomers = Booking::distinct('user_id')->count();
        return view('home', compact('featuredRooms', 'totalBookings', 'totalCustomers'));
    }
}
