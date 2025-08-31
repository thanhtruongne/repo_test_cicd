@extends('layouts.admin')

@section('title', 'Notification Detail')
@section('notifications-active', 'active')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Contact Detail</h1>
        <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary">Back</a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Notification Content</h6>
                </div>
                <div class="card-body">
                    <h4>{{ $notification->subject ?: 'Consultation Request' }}</h4>
                    <div class="border rounded p-3 bg-light mt-3">
                        <p style="white-space: pre-wrap;">{{ $notification->message }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Contact Information</h6>
                </div>
                <div class="card-body">
                    @if($notification->user)
                    <p><strong>Member:</strong> {{ $notification->user->name }}</p>
                    @endif
                    <p><strong>Name:</strong> {{ $notification->name }}</p>
                    <p><strong>Email:</strong> 
                        <a href="mailto:{{ $notification->email }}">{{ $notification->email }}</a>
                    </p>
                    @if($notification->phone)
                    <p><strong>Phone:</strong> 
                        <a href="tel:{{ $notification->phone }}">{{ $notification->phone }}</a>
                    </p>
                    @endif
                    <p><strong>Submitted At:</strong> {{ $notification->created_at->format('d/m/Y H:i') }}</p>
                    @if($notification->read_at)
                    <p><strong>Read At:</strong> {{ $notification->read_at->format('d/m/Y H:i') }}</p>
                    @endif
                    <p><strong>Status:</strong> 
                        @if($notification->is_read)
                            <span class="badge bg-success">Read</span>
                        @else
                            <span class="badge bg-warning">Unread</span>
                        @endif
                    </p>

                    <hr>

                    <div class="d-grid gap-2">
                        {{-- <a href="mailto:{{ $notification->email }}" class="btn btn-primary btn-sm">
                            Reply via Email
                        </a>
                        @if($notification->phone)
                        <a href="tel:{{ $notification->phone }}" class="btn btn-success btn-sm">
                            Call
                        </a>
                        @endif --}}
                        <button onclick="deleteNotification({{ $notification->id }})" class="btn btn-danger btn-sm">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function deleteNotification(id) {
    if (confirm('Are you sure you want to delete this notification?')) {
        fetch(`/admin/notifications/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            }
        }).then(() => {
            window.location.href = '{{ route("admin.notifications.index") }}';
        });
    }
}
</script>
@endsection
