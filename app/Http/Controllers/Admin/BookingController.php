<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingLog;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use GuzzleHttp\Client;

class BookingController extends Controller
{
    private function logBooking($bookingId, $fieldName, $oldValue, $newValue, $actionType = 'update')
    {
        BookingLog::create([
            'booking_id' => $bookingId,
            'user_id' => Auth::id(),
            'field_name' => $fieldName,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'action_type' => $actionType,
            'ip_address' => request()->ip()
        ]);
    }


    public function index(Request $request)
    {
        $bookingsQuery = Booking::with(['user', 'room']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $bookingsQuery->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                    ->orWhereHas('room', function ($rq) use ($search) {
                        $rq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $bookings = $bookingsQuery->latest()->paginate(10);
        $users = User::orderBy('name')->get();
        $rooms = Room::orderBy('name')->get();
        return view('admin.booking.index', compact('bookings', 'users', 'rooms'));
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id'         => 'required|exists:users,id',
                'room_id'         => 'required|exists:rooms,id',
                'booking_date'    => 'required|date|after_or_equal:today',
                'end_time'        => 'required|date|after_or_equal:booking_date',
                'start_time'      => 'required',
                'duration'        => 'required|integer|min:1|max:12',
                'payment_method'  => 'required|in:cash,bank_transfer,credit_card',
                'notes'           => 'nullable|string|max:1000'
            ], [
                'booking_date.required' => 'Vui lòng chọn ngày đặt phòng',
                'duration.max'          => 'Thời gian thuê tối đa là 12 giờ',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $startDateTime = Carbon::parse($request->booking_date . ' ' . $request->start_time);
            $endDateTime = Carbon::parse($request->end_time);

            if ($startDateTime->hour < 8 || $endDateTime->hour > 22 || $endDateTime->isSameDay($startDateTime) === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Giờ đặt phòng phải nằm trong khoảng 8:00 - 22:00 cùng ngày'
                ], 422);
            }

            $actualDurationInMinutes = $startDateTime->diffInMinutes($endDateTime);
            $expectedDurationInMinutes = $request->duration * 60;

            if ($actualDurationInMinutes !== $expectedDurationInMinutes) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thời lượng không khớp với thời gian bắt đầu và kết thúc'
                ], 422);
            }

            $conflict = Booking::where('room_id', $request->room_id)
                ->where('status', '!=', 'cancelled')
                ->where(function ($query) use ($startDateTime, $endDateTime) {
                    $query->whereBetween('start_time', [$startDateTime, $endDateTime])
                        ->orWhereBetween('end_time', [$startDateTime, $endDateTime])
                        ->orWhere(function ($q) use ($startDateTime, $endDateTime) {
                            $q->where('start_time', '<=', $startDateTime)
                                ->where('end_time', '>=', $endDateTime);
                        });
                })->exists();

            if ($conflict) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phòng đã được đặt trong khoảng thời gian này'
                ], 422);
            }

            $room = Room::findOrFail($request->room_id);
            $totalAmount = $room->hourly_rate * $request->duration;

            $booking = Booking::create([
                'user_id'        => $request->user_id,
                'room_id'        => $request->room_id,
                'start_time'     => $startDateTime,
                'end_time'       => $endDateTime,
                'total_hours'    => $request->duration,
                'total_amount'   => $totalAmount,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'status'         => 'pending',
                'notes'          => $request->notes,
            ]);

            // Log booking creation
            $this->logBooking($booking->id, 'status', null, 'pending', 'create');

            return response()->json([
                'success' => true,
                'message' => 'Đặt phòng thành công',
                'booking_id' => $booking->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {

        try {
            $booking = Booking::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'user_id'        => 'required|exists:users,id',
                'room_id'        => 'required|exists:rooms,id',
                'booking_date'   => 'required|date|after_or_equal:today',
                'end_time'       => 'required|date|after_or_equal:booking_date',
                'start_time'     => 'required',
                'duration'       => 'required|integer|min:1|max:12',
                'notes'          => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $start = Carbon::parse($request->booking_date . ' ' . $request->start_time);
            $end = Carbon::parse($request->end_time);

            if ($start->hour < 8 || $end->hour > 22 || !$end->isSameDay($start)) {
                return response()->json(['success' => false, 'message' => 'Giờ đặt phòng phải nằm trong khoảng 8:00 - 22:00 cùng ngày'], 422);
            }
            $room = Room::findOrFail($request->room_id);
            $totalAmount = $room->hourly_rate * $request->duration;
            $oldTotalAmount = +$booking->total_amount;

            $data = [
                'user_id'        => $request->user_id,
                'room_id'        => $request->room_id,
                'start_time'     => $start,
                'end_time'       => $end,
                'total_hours'    => $request->duration,
                'total_amount'   => $totalAmount,
                'payment_method' => $booking->payment_method,
                'notes'          => $request->notes,
            ];

            $booking->fill($data);
            if ($booking->payment_method == 'balance' && $booking->transaction_id && $booking->isDirty('total_amount') && $totalAmount != $oldTotalAmount) {
                $client = new Client();
                $tokenData = $client->request('POST', env('ORDER_API_URL') . '3rd/user/get-data-user', [
                    'form_params' => [
                        'email' => $booking->user->email,
                        'isToken' => true
                    ]
                ]);
                $token = json_decode($tokenData->getBody(), true);
                $priceToTal = $totalAmount > $oldTotalAmount ?  $totalAmount - $oldTotalAmount : $oldTotalAmount - $totalAmount;
                $type = $totalAmount > $oldTotalAmount ? "payment" : "refund";
                $apiResponse = $client->request('POST', env('ORDER_API_URL') . 'admin/transaction/payment', [
                    'headers' => [
                        'x-api-key' => env('ORDER_API_KEY'),
                        'Authorization' => 'Bearer ' . $token['data'],
                    ],
                    'form_params' => [
                        'action' => $type,
                        'amount' => $priceToTal,
                        'transaction_id' => $booking->transaction_id
                    ]
                ]);
                $response = json_decode($apiResponse->getBody(), true);
                if (!$response['status']) {
                    return response()->json(['success' => false, 'message' => $response['message']], 422);
                }

                // if ($type == 'payment') {
                //     $booking->transaction_id = $response['data']['id']; //update lại case transaction_id
                // }
            }


            $booking->save();

            return response()->json(['success' => true, 'message' => 'Cập nhật thành công']);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra.']);
        }
    }


    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed',
        ]);

        $booking = Booking::findOrFail($id);
        $oldStatus = $booking->status;

        $booking->status = $request->status;
        if ($request->status == 'cancelled' && $booking->payment_method == 'balance') {
            if (!$booking->transaction_id) {
                return response()->json(['success' => false, 'message' => 'Cannot update status']);
            }
            $client = new Client();
            $tokenData = $client->request('POST', env('ORDER_API_URL') . '3rd/user/get-data-user', [
                'form_params' => [
                    'email' => $booking->user->email,
                    'isToken' => true
                ]
            ]);
            $token = json_decode($tokenData->getBody(), true);
            $apiResponse = $client->request('POST', env('ORDER_API_URL') . 'admin/transaction/payment', [
                'headers' => [
                    'x-api-key' => env('ORDER_API_KEY'),
                    'Authorization' => 'Bearer ' . $token['data'],
                ],
                'form_params' => [
                    'action' => 'refund',
                    'amount' => +$booking->total_amount,
                    'transaction_id' => $booking->transaction_id
                ]
            ]);
            $response = json_decode($apiResponse->getBody(), true);
            if (!$response['status']) {
                return response()->json(['success' => false, 'message' => $response['message']], 422);
            }
        }
        $booking->save();
        return response()->json(['success' => true, 'message' => 'Status updated']);
    }

    public function updatePaymentStatus(Request $request, $id)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,cancelled',
        ]);

        $booking = Booking::findOrFail($id);
        $oldPaymentStatus = $booking->payment_status;

        $booking->payment_status = $request->payment_status;
        $booking->save();

        // Log payment status change
        $this->logBooking($booking->id, 'payment_status', $oldPaymentStatus, $request->payment_status);

        return response()->json([
            'success' => true,
            'message' => 'Payment status updated successfully.',
        ]);
    }

    public function updatePaymentMethod(Request $request, $id)
    {
        try {
            $request->validate([
                'payment_method' => 'required|in:cash,bank_transfer,balance',
            ]);

            $booking = Booking::findOrFail($id);
            $oldPaymentMethod = $booking->payment_method;

            $booking->payment_method = $request->payment_method;
            $booking->save();

            // Log payment method change
            $this->logBooking($booking->id, 'payment_method', $oldPaymentMethod, $request->payment_method);

            return response()->json([
                'success' => true,
                'message' => 'Phương thức thanh toán đã được cập nhật.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);

        // Log deletion before deleting
        $this->logBooking($booking->id, 'status', $booking->status, 'deleted', 'delete');

        $booking->delete();

        return response()->json(['success' => true, 'message' => 'Booking deleted successfully.']);
    }

    public function allBookingsApi(Request $request)
    {
        $bookings = Booking::with(['room', 'user'])
            ->get();

        $events = $bookings->map(function ($booking) {
            return [
                'id'    => $booking->id,
                'user_name' => optional($booking->user)->name,
                'title' => ($booking->user ? $booking->user->name : 'Đã đặt') . ' - ' . ($booking->room ? $booking->room->name : ''),
                'start' => $booking->start_time->toIso8601String(),
                'end'   => $booking->end_time->toIso8601String(),
                'roomId' => $booking->room_id,
                'room_name' => $booking->room ? $booking->room->name : 'Không xác định',
                'status' => $booking->status,
            ];
        });

        return response()->json($events);
    }
}
