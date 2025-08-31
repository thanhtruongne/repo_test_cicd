<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Danh sách thông báo
    public function index(Request $request)
    {
        $query = Notification::latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'read') {
                $query->where('is_read', true);
            } elseif ($request->status === 'unread') {
                $query->where('is_read', false);
            }
        }

        $notifications = $query->paginate(10);
        
        return view('admin.notification.index', compact('notifications'));
    }

    public function show($id)
    {
        $notification = Notification::findOrFail($id);
        
        // Đánh dấu đã đọc khi xem
        if (!$notification->is_read) {
            $notification->markAsRead();
        }

        return view('admin.notification.show', compact('notification'));
    }

    // Đánh dấu đã đọc
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    // Xóa
    public function destroy($id)
    {
        Notification::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}