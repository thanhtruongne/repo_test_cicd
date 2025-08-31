@extends('layouts.admin')

@section('title', 'Notifications')
@section('notifications-active', 'active')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Contact</h1>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Search..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>Unread</option>
                        <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>Read</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card shadow">
        <div class="card-body">
            @if($notifications->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Subject</th>
                                <th>Time</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($notifications as $notification)
                            <tr class="{{ !$notification->is_read ? 'table-warning' : '' }}">
                                <td class="text-center">
                                    @if($notification->is_read)
                                        <span class="badge bg-success">Read</span>
                                    @else
                                        <span class="badge bg-warning">Unread</span>
                                    @endif
                                </td>
                                <td>{{ $notification->name }}</td>
                                <td>{{ $notification->email }}</td>
                                <td>{{ $notification->subject ?: 'Consultation Request' }}</td>
                                <td>{{ $notification->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.notifications.show', $notification->id) }}" 
                                       class="btn btn-info btn-sm">View</a>

                                    @if(!$notification->is_read)
                                        <button onclick="markAsRead({{ $notification->id }})" 
                                                class="btn btn-success btn-sm">Mark as Read</button>
                                    @endif

                                    <button onclick="deleteNotification({{ $notification->id }})" 
                                            class="btn btn-danger btn-sm">Delete</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{ $notifications->links() }}
            @else
                <div class="text-center py-4">
                    <p>No notifications found.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function markAsRead(id) {
    fetch(`/admin/notifications/${id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        }
    }).then(() => location.reload());
}

function deleteNotification(id) {
    if (confirm('Are you sure you want to delete this notification?')) {
        fetch(`/admin/notifications/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            }
        }).then(() => location.reload());
    }
}
</script>
@endsection
