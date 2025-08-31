<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'room_id' => 'required|exists:rooms,id',
                'booking_date' => 'required|date|after_or_equal:today',
                'start_time' => 'required',
                'duration' => 'required|integer|min:1|max:12',
                'payment_method' => 'required|in:cash,bank_transfer,balance',
                'notes' => 'nullable|string'
            ], [
                // Custom error messages
                'booking_date.required' => 'Vui lòng chọn ngày đặt phòng',
                'booking_date.date' => 'Ngày đặt phòng không hợp lệ',
                'booking_date.after_or_equal' => 'Ngày đặt phòng phải là hôm nay hoặc trong tương lai',
                'start_time.required' => 'Vui lòng chọn giờ bắt đầu',
                'duration.required' => 'Vui lòng chọn thời gian thuê',
                'duration.min' => 'Thời gian thuê tối thiểu là 1 giờ',
                'duration.max' => 'Thời gian thuê tối đa là 12 giờ',
                'payment_method.required' => 'Vui lòng chọn phương thức thanh toán',
            ]);

            // Check booking time validity
            $startDateTime = Carbon::parse($request->booking_date . ' ' . $request->start_time);
            $endDateTime = $startDateTime->copy()->addHours($request->duration);

            if ($startDateTime->hour < 8 || $endDateTime->hour > 22) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thời gian hoạt động của phòng từ 8:00 đến 22:00'
                ], 422);
            }

            // Check for overlapping bookings
            $existingBooking = Booking::where('room_id', $request->room_id)
                ->where('status', '!=', 'cancelled')
                ->where(function ($query) use ($startDateTime, $endDateTime) {
                    $query->whereBetween('start_time', [$startDateTime, $endDateTime])
                        ->orWhereBetween('end_time', [$startDateTime, $endDateTime])
                        ->orWhere(function ($q) use ($startDateTime, $endDateTime) {
                            $q->where('start_time', '<=', $startDateTime)
                                ->where('end_time', '>=', $endDateTime);
                        });
                })->exists();

            if ($existingBooking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phòng đã được đặt trong khoảng thời gian này'
                ], 422);
            }

            $total_amount = $request->duration * Room::find($request->room_id)->hourly_rate;
            if ($request->payment_method == 'balance') {
                $currentBalance = session('balance_' . auth()->id());
                $client = new Client();
                if ($currentBalance  < $total_amount) {
                    return response()->json(['status' => false, 'message' => 'Số dư tài khoản không đủ.']);
                }
                $tokenData = $client->request('POST', env('ORDER_API_URL') . '3rd/user/get-data-user', [
                    'headers' => [
                        'x-api-key' => env('ORDER_API_KEY'),
                    ],
                    'form_params' => [
                        'email' => auth()->user()->email,
                        'isToken' => true
                    ]
                ]);
                $token = json_decode($tokenData->getBody(), true);
                if (!$token['data']) {
                    return response()->json(['message' => 'Không thể lấy thông tin người dùng', 'status' => false], 419);
                }

                $apiResponse = $client->request('POST', env('ORDER_API_URL') . 'admin/transaction/payment', [
                    'headers' => [
                        'x-api-key' => env('ORDER_API_KEY'),
                        'Authorization' => 'Bearer ' . $token['data'],
                    ],
                    'form_params' => [
                        'action' => 'payment',
                        'amount' => $total_amount
                    ]
                ]);
                $dataResponse = json_decode($apiResponse->getBody(), true);
                if ($dataResponse['status'] && $dataResponse['data']['id']) {
                    $booking = Booking::create([
                        'user_id' => auth()->id(),
                        'room_id' => $request->room_id,
                        'start_time' => $startDateTime,
                        'end_time' => $endDateTime,
                        'total_hours' => $request->duration,
                        'total_amount' => $total_amount,
                        'payment_method' => $request->payment_method,
                        'payment_status' => 'paid',
                        'status' => 'pending',
                        'notes' => $request->notes,
                        'transaction_id' => $dataResponse['data']['id']
                    ]);
                } else {
                    return response()->json(['message' => 'Thanh toán không thành công', 'status' => false], 422);
                }
                session()->put('balance_' . auth()->id(), +$dataResponse['data']['current_balance']);
                Log::info("Payment with balance amount " . $total_amount . " with transaction " . $dataResponse['data']['id']);
            } else {
                // Create booking
                $booking = Booking::create([
                    'user_id' => auth()->id(),
                    'room_id' => $request->room_id,
                    'start_time' => $startDateTime,
                    'end_time' => $endDateTime,
                    'total_hours' => $request->duration,
                    'total_amount' => $total_amount,
                    'payment_method' => $request->payment_method,
                    'payment_status' => 'pending',
                    'status' => 'pending',
                    'notes' => $request->notes
                ]);
            }
            return response()->json(['success' => true, 'redirect' => route('bookings.show', $booking->id)]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra. Vui lòng thử lại sau.',
                'stack' => $e->getMessage()
            ], 500);
        }
    }


    public function show(Booking $booking)
    {
        // Kiểm tra quyền xem
        return view('bookings.show', compact('booking'));
    }

    public function index()
    {
        $bookings = Booking::where('user_id', auth()->id())
            ->with('room')
            ->latest()
            ->paginate(10);


        return view('bookings.index', compact('bookings'));
    }

    public function cancel(Booking $booking)
    {
        // Kiểm tra quyền hủy
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        if ($booking->payment_method == 'balance' && is_null($booking->transaction_id)) {
            return back()->with('error', 'Không thể hủy đơn này');
        }

        // Chỉ cho phép hủy booking pending
        if ($booking->status !== 'pending') {
            return back()->with('error', 'Không thể hủy đơn này');
        }

        if ($booking->payment_method == 'balance') {
            $client = new Client();
            $tokenData = $client->request('POST', env('ORDER_API_URL') . '3rd/user/get-data-user', [
                'headers' => [
                    'x-api-key' => env('ORDER_API_KEY'),
                ],
                'form_params' => [
                    'email' => auth()->user()->email,
                    'isToken' => true
                ]
            ]);
            $token = json_decode($tokenData->getBody(), true);
            if (!$token['data']) {
                return response()->json(['message' => 'Không thể lấy thông tin người dùng', 'status' => false], 419);
            }
            $apiResponse = $client->request('POST', env('ORDER_API_URL') . 'admin/transaction/payment', [
                'headers' => [
                    'x-api-key' => env('ORDER_API_KEY'),
                    'Authorization' => 'Bearer ' . $token['data'],
                ],
                'form_params' => [
                    'action' => 'refund',
                    'amount' => $booking->total_amount,
                    'transaction_id' => $booking->transaction_id
                ]
            ]);
            $dataResponse = json_decode($apiResponse->getBody(), true);

            if ($dataResponse['status'] && $dataResponse['data']['id']) {
                session()->put('balance_' . auth()->id(), +$dataResponse['data']['current_balance']);
                Log::info("Payment refund with balance amount with transaction " . $dataResponse['data']['id']);
                $booking->update([
                    'status' => 'cancelled'
                ]);
                return redirect()->route('bookings.index')
                    ->with('success', 'Đã hủy đặt phòng thành công');
            }
        } else {
            $booking->update([
                'status' => 'cancelled'
            ]);
            return redirect()->route('bookings.index')
                ->with('success', 'Đã hủy đặt phòng thành công');
        }

        return back()->with('error', 'Không thể hủy đơn này');
    }
}
