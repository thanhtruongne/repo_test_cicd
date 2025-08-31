<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    //
    public function index(Request $request)
    {
        $roomsQuery = Room::withCount('bookings');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $roomsQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $rooms = $roomsQuery->latest()->paginate(10);
        return view('admin.room.index', compact('rooms'));
    }

public function store(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:rooms,name',
            'description' => 'nullable|string|max:1000',
            'hourly_rate' => 'required|numeric|min:0',
            'status' => 'required|in:active,maintenance,inactive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $fileName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path(), $fileName);
            $imagePath = $fileName;
        }

        $room = Room::create([
            'name' => $request->name,
            'description' => $request->description,
            'hourly_rate' => $request->hourly_rate,
            'status' => $request->status,
            'image_url' => $imagePath,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tạo phòng thành công',
            'room_id' => $room->id
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
            $room = Room::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:rooms,name,' . $id,
                'description' => 'nullable|string|max:1000',
                'hourly_rate' => 'required|numeric|min:0',
                'status' => 'required|in:active,maintenance,inactive',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $imagePath = $room->image_url;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $fileName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path(), $fileName);
                $imagePath = $fileName;
            }

            $room->update([
                'name' => $request->name,
                'description' => $request->description,
                'hourly_rate' => $request->hourly_rate,
                'status' => $request->status,
                'image_url' => $imagePath,
            ]);

            return response()->json(['success' => true, 'message' => 'Cập nhật phòng thành công']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:active,maintenance,inactive',
            ]);

            $room = Room::findOrFail($id);
            $room->status = $request->status;
            $room->save();

            return response()->json(['success' => true, 'message' => 'Trạng thái phòng đã được cập nhật']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:active,maintenance,inactive',
            ]);

            $room = Room::findOrFail($id);
            $room->status = $request->status;
            $room->save();

            return response()->json(['success' => true, 'message' => 'Trạng thái phòng đã được cập nhật']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $room = Room::findOrFail($id);

            // Kiểm tra xem phòng có booking nào không
            if ($room->bookings()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa phòng vì còn booking liên quan'
                ], 422);
            }

            $room->delete();

            return response()->json(['success' => true, 'message' => 'Đã xóa phòng thành công.']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
}
