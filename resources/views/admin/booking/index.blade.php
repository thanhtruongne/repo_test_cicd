@extends('layouts.admin')
@section('booking-active', 'active')
@section('page-title', 'Booking Management')
@section('styles')
    <style>
        .booking-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(45deg, #00c9ff, #92fe9d);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        .form-select-sm {
            min-width: 120px !important;
            font-size: 13px !important;
        }

        /* Đảm bảo các cột dropdown đủ rộng */
        .table td:nth-child(6),
        .table td:nth-child(7),
        .table td:nth-child(8) {
            min-width: 130px !important;
            white-space: nowrap !important;
        }

        /* Không xuống dòng cho toàn bộ bảng, giữ nguyên chiều ngang, cuộn ngang nếu tràn */
        .table td,
        .table th {
            white-space: nowrap !important;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 180px;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .form-label.required::after {
            content: " *";
            color: red;
        }
        /* Filter form styling */
        .form-control-sm, .form-select-sm {
            font-size: 13px !important;
            padding: 0.25rem 0.5rem !important;
        }

        /* Filter badges styling */
        .badge a {
            text-decoration: none;
            font-weight: bold;
        }

        .badge a:hover {
            text-decoration: underline;
        }

        /* Responsive filters */
        @media (max-width: 768px) {
            .row.g-2 {
                --bs-gutter-x: 0.5rem;
            }

            .form-control-sm, .form-select-sm {
                min-width: auto !important;
                width: 100%;
            }

            .col-auto {
                flex: 1;
                min-width: 0;
            }
        }

        /* Filter summary */
        .badge {
            font-size: 12px;
            padding: 0.375rem 0.75rem;
        }

        /* Search form alignment */
        .d-flex.align-items-center .row {
            margin: 0;
        }

        .d-flex.align-items-center .col-auto {
            padding-left: 0.25rem;
            padding-right: 0.25rem;
        }
        .form-control-sm, .form-select-sm {
            font-size: 13px !important;
            padding: 0.25rem 0.5rem !important;
        }
    </style>
@endsection

@section('content')
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">All Bookings</h4>
            <div class="d-flex align-items-center">
                <!-- Enhanced Search Form với filters -->
                <form method="GET" action="{{ route('admin.booking.index') }}" class="me-2">
                    <div class="row g-2 align-items-end">
                        <!-- Search by text -->
                        <div class="col-auto">
                            <input type="text" name="search" class="form-control form-control-sm"
                                placeholder="Search booking..." value="{{ request('search') }}" style="min-width: 200px;">
                        </div>

                        <!-- Filter by date -->
                        <div class="col-auto">
                            <input type="date" name="date" class="form-control form-control-sm"
                                value="{{ request('date') }}" title="Filter by booking date">
                        </div>

                        <!-- Filter by room -->
                        <div class="col-auto">
                            <select name="room_id" class="form-select form-select-sm" style="min-width: 150px;">
                                <option value="">All Rooms</option>
                                @foreach ($rooms as $room)
                                    <option value="{{ $room->id }}"
                                        {{ request('room_id') == $room->id ? 'selected' : '' }}>
                                        {{ $room->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filter by status -->
                        <div class="col-auto">
                            <select name="status" class="form-select form-select-sm" style="min-width: 120px;">
                                <option value="">All Status</option>
                                @foreach (['pending', 'confirmed', 'cancelled', 'completed'] as $status)
                                    <option value="{{ $status }}"
                                        {{ request('status') == $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Search button -->
                        <div class="col-auto">
                            <button class="btn btn-outline-secondary btn-sm" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>

                        <!-- Clear filters button -->
                        <div class="col-auto">
                            <a href="{{ route('admin.booking.index') }}" class="btn btn-outline-danger btn-sm"
                                title="Clear filters">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>
                </form>

                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBookingModal">
                    <i class="fas fa-plus"></i> Add Booking
                </button>
            </div>
        </div>

        <!-- Filter summary (hiển thị các filter đang active) -->
        @if (request()->hasAny(['search', 'date', 'room_id', 'status']))
            <div class="mb-3">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="text-muted">Active filters:</span>

                    @if (request('search'))
                        <span class="badge bg-info">
                            Search: "{{ request('search') }}"
                            <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="text-white ms-1">×</a>
                        </span>
                    @endif

                    @if (request('date'))
                        <span class="badge bg-primary">
                            Date: {{ \Carbon\Carbon::parse(request('date'))->format('d/m/Y') }}
                            <a href="{{ request()->fullUrlWithQuery(['date' => null]) }}" class="text-white ms-1">×</a>
                        </span>
                    @endif

                    @if (request('room_id'))
                        @php
                            $selectedRoom = $rooms->firstWhere('id', request('room_id'));
                        @endphp
                        <span class="badge bg-success">
                            Room: {{ $selectedRoom ? $selectedRoom->name : 'Unknown' }}
                            <a href="{{ request()->fullUrlWithQuery(['room_id' => null]) }}" class="text-white ms-1">×</a>
                        </span>
                    @endif

                    @if (request('status'))
                        <span class="badge bg-warning text-dark">
                            Status: {{ ucfirst(request('status')) }}
                            <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="text-white ms-1">×</a>
                        </span>
                    @endif
                </div>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Room</th>
                        <th>Start Day</th>
                        <th>End Time</th>
                        <th>Hours</th>
                        <th>Note</th>
                        <th>Payment_Method</th>
                        <th>Payment_Status</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        <tr>
                            <td>#{{ $booking->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    {{-- <div class="booking-avatar me-2">
                            {{ strtoupper(substr($booking->user->name ?? 'U', 0, 1)) }}
                        </div> --}}
                                    <div>
                                        <div class="fw-bold">{{ $booking->user->name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $booking->user->email ?? '' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $booking->room->name ?? 'N/A' }}</td>
                            <td>
                                {{-- Start Day --}}
                                {{ $booking->start_time ? $booking->start_time->format('d/m/Y H:i') : '' }}
                            </td>
                            <td>
                                {{-- End Time --}}
                                {{ $booking->end_time ? $booking->end_time->format('d/m/Y H:i') : '' }}
                            </td>
                            <td>
                                {{-- Hours --}}
                                {{ $booking->total_hours }}
                            </td>
                            <td>{{ $booking->notes ?? 'N/A' }}</td>
                            <td>
                                <select class="form-select form-select-sm payment-method-dropdown"
                                    @disabled($booking->payment_method == 'balance')
                                    data-id="{{ $booking->id }}">
                                    <option value="cash" {{ $booking->payment_method == 'cash' ? 'selected' : '' }}>
                                        Cash</option>
                                    <option value="balance" disabled {{ $booking->payment_method == 'balance' ? 'selected' : '' }}>
                                        Balance</option>
                                    <option value="bank_transfer"
                                        {{ $booking->payment_method == 'bank_transfer' ? 'selected' : '' }}>
                                        Bank Transfer</option>
                                </select>
                            </td>
                            <td>
                                <select class="form-select form-select-sm payment-status-dropdown"
                                    data-id="{{ $booking->id }}">
                                    <option value="pending" {{ $booking->payment_status == 'pending' ? 'selected' : '' }}>
                                        Pending</option>
                                    <option value="paid" {{ $booking->payment_status == 'paid' ? 'selected' : '' }}>Paid
                                    </option>
                                    <option value="cancelled"
                                        {{ $booking->payment_status == 'cancelled' ? 'selected' : '' }}>
                                        Cancelled</option>
                                </select>
                            </td>
                            <td>
                                <select class="form-select form-select-sm status-dropdown" data-id="{{ $booking->id }}">
                                    @foreach (['pending', 'confirmed', 'cancelled', 'completed'] as $status)
                                        <option value="{{ $status }}"
                                            {{ $booking->status === $status ? 'selected' : '' }}>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                {{-- Format VND --}}
                                {{ number_format($booking->total_amount, 0, ',', '.') }} đ
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-info viewBookingBtn"
                                        data-id="{{ $booking->id }}" data-user="{{ $booking->user->name ?? 'N/A' }}"
                                        data-useremail="{{ $booking->user->email ?? '' }}"
                                        data-room="{{ $booking->room->name ?? 'N/A' }}"
                                        data-start="{{ $booking->start_time ? $booking->start_time->format('d/m/Y H:i') : '' }}"
                                        data-end="{{ $booking->end_time ? $booking->end_time->format('d/m/Y H:i') : '' }}"
                                        data-hours="{{ $booking->total_hours }}"
                                        data-payment_method="{{ $booking->payment_method }}"
                                        data-payment_status="{{ $booking->payment_status }}"
                                        data-status="{{ $booking->status }}"
                                        data-total="{{ number_format($booking->total_amount, 0, ',', '.') }} đ"
                                        data-notes="{{ $booking->notes ?? '' }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger"
                                        onclick="deleteBooking({{ $booking->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning editBookingBtn" data-id="{{ $booking->id }}"
                                        data-user_id="{{ $booking->user_id }}" data-room_id="{{ $booking->room_id }}"
                                        data-date="{{ $booking->start_time->format('Y-m-d') }}"
                                        data-start="{{ $booking->start_time->format('H:i') }}"
                                        data-end="{{ $booking->end_time ? $booking->end_time->format('Y-m-d\TH:i') : '' }}"
                                        data-duration="{{ $booking->total_hours }}"
                                        data-method="{{ $booking->payment_method }}" data-notes="{{ $booking->notes }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center text-muted py-4">No bookings found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $bookings->links() }}
        </div>

        <!-- Add Booking Modal -->
        <div class="modal fade" id="addBookingModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.booking.store') }}" method="POST" id="addBookingForm">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Add New Booking </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label required">User </label>
                                <select class="form-select" name="user_id" required>
                                    <option value="">-- Select User --</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Room </label>
                                <select class="form-select" name="room_id" required>
                                    <option value="">-- Select Room --</option>
                                    @foreach ($rooms as $room)
                                        <option value="{{ $room->id }}">{{ $room->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Booking Date </label>
                                <input type="date" name="booking_date" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Start Time </label>
                                <input type="time" name="start_time" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">End Time </label>
                                <input type="datetime-local" name="end_time" class="form-control" required>
                            </div>


                            <div class="mb-3">
                                <label class="form-label required">Duration (hours) </label>
                                <input type="number" name="duration" class="form-control" min="1"
                                    max="12" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Payment Method </label>
                                <select class="form-select" name="payment_method" required>
                                    <option value="cash">Cash</option>
                                    <option value="credit_card">Credit Card</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control" name="notes" rows="3"></textarea>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Booking
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Edit - FIXED VERSION -->
        <div class="modal fade" id="editBookingModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form id="editBookingForm" method="POST">
                    @csrf
                    <input type="hidden" name="booking_id" id="edit-booking-id">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Booking</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">User:</label>
                                <select name="user_id" id="edit-booking-user" class="form-control">
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            {{--
                            <div class="mb-3">
                                <label class="form-label">Room:</label>
                                <select name="room_id" id="edit-booking-room" class="form-control">
                                    @foreach ($rooms as $room)
                                        <option value="{{ $room->id }}">{{ $room->name }}</option>
                                    @endforeach
                                </select>
                            </div> --}}
                            <div class="mb-3">
                                <label class="form-label">Room:</label>
                                <input type="text" id="edit-booking-room-display" class="form-control" readonly
                                    style="background-color: #f8f9fa;">
                                <input type="hidden" name="room_id" id="edit-booking-room-id">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Booking Date:</label>
                                <input type="date" name="booking_date" id="edit-booking-date" class="form-control">
                            </div>


                            <div class="mb-3">
                                <label class="form-label">Start Time:</label>
                                <input type="time" name="start_time" id="edit-booking-start" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">End Time:</label>
                                <input type="datetime-local" name="end_time" id="edit-booking-endtime"
                                    class="form-control">
                            </div>


                            <div class="mb-3">
                                <label class="form-label">Duration (hours):</label>
                                <input type="number" name="duration" id="edit-booking-duration" class="form-control"
                                    min="1" max="12" step="1">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Notes:</label>
                                <textarea name="notes" id="edit-booking-notes" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Cập nhật</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Xem Chi Tiết Booking --}}
        <div class="modal fade" id="viewBookingModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Chi tiết Booking</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered mb-0">
                            <tbody>
                                <tr>
                                    <th>ID</th>
                                    <td id="detail-id"></td>
                                </tr>
                                <tr>
                                    <th>User</th>
                                    <td>
                                        <span id="detail-user"></span>
                                        <br>
                                        <small id="detail-useremail" class="text-muted"></small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Room</th>
                                    <td id="detail-room"></td>
                                </tr>
                                <tr>
                                    <th>Start</th>
                                    <td id="detail-start"></td>
                                </tr>
                                <tr>
                                    <th>End</th>
                                    <td id="detail-end"></td>
                                </tr>
                                <tr>
                                    <th>Hours</th>
                                    <td id="detail-hours"></td>
                                </tr>
                                <tr>
                                    <th>Payment Method</th>
                                    <td id="detail-payment_method"></td>
                                </tr>
                                <tr>
                                    <th>Payment Status</th>
                                    <td id="detail-payment_status"></td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td id="detail-status"></td>
                                </tr>
                                <tr>
                                    <th>Total</th>
                                    <td id="detail-total"></td>
                                </tr>
                                <tr>
                                    <th>Notes</th>
                                    <td id="detail-notes"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')

    <script>
        // Hàm kiểm tra và disable/enable các dropdown dựa trên status
        function updateDropdownStates() {
            document.querySelectorAll('.status-dropdown').forEach(statusSelect => {
                const bookingId = statusSelect.dataset.id;
                const currentStatus = statusSelect.value;

                // Lấy thêm thông tin payment_status và payment_method từ dataset hoặc DOM
                const paymentStatusDropdown = document.querySelector(
                    `.payment-status-dropdown[data-id="${bookingId}"]`);
                const paymentMethodDropdown = document.querySelector(
                    `.payment-method-dropdown[data-id="${bookingId}"]`);

                const currentPaymentStatus = paymentStatusDropdown ? paymentStatusDropdown.value : '';
                const currentPaymentMethod = paymentMethodDropdown ? paymentMethodDropdown.value : '';

                // Nếu status đã là completed hoặc cancelled → disable status dropdown
                if (currentStatus === 'completed' || currentStatus === 'cancelled') {
                    statusSelect.disabled = true;
                    statusSelect.style.opacity = '0.6';
                    statusSelect.style.cursor = 'not-allowed';
                } else {
                    // Nếu status khác completed/cancelled → enable status dropdown
                    statusSelect.disabled = false;
                    statusSelect.style.opacity = '1';
                    statusSelect.style.cursor = 'pointer';
                }

                if (paymentStatusDropdown) {
                    // Nếu đã thanh toán (paid) → không được đổi payment_status
                    if (currentPaymentStatus === 'paid') {
                        paymentStatusDropdown.disabled = true;
                        paymentStatusDropdown.style.opacity = '0.6';
                        paymentStatusDropdown.style.cursor = 'not-allowed';
                    } else {
                        // Nếu chưa thanh toán → cho phép đổi payment_status
                        paymentStatusDropdown.disabled = false;
                        paymentStatusDropdown.style.opacity = '1';
                        paymentStatusDropdown.style.cursor = 'pointer';
                    }
                }

                if (paymentMethodDropdown) {
                    // Nếu đã thanh toán VÀ phương thức là balance → không được đổi
                    if (currentPaymentStatus === 'paid' && currentPaymentMethod === 'balance') {
                        paymentMethodDropdown.disabled = true;
                        paymentMethodDropdown.style.opacity = '0.6';
                        paymentMethodDropdown.style.cursor = 'not-allowed';
                    } else {
                        paymentMethodDropdown.disabled = false;
                        paymentMethodDropdown.style.opacity = '1';
                        paymentMethodDropdown.style.cursor = 'pointer';
                    }
                }
            });
        }

        // Hàm tính toán duration dựa trên start và end time
        function calculateDuration(startDateTime, endDateTime) {
            if (!startDateTime || !endDateTime) return 0;

            const start = new Date(startDateTime);
            const end = new Date(endDateTime);

            if (end <= start) return 0;

            const diffMs = end - start;
            const diffHours = diffMs / (1000 * 60 * 60); // Convert milliseconds to hours

            return Math.round(diffHours * 100) / 100; // Round to 2 decimal places
        }

        // Hàm tính end time dựa trên start time và duration
        function calculateEndTime(startDateTime, durationHours) {
            if (!startDateTime || !durationHours || durationHours <= 0) return '';

            const start = new Date(startDateTime);
            const end = new Date(start.getTime() + (durationHours * 60 * 60 * 1000));

            // Format thành datetime-local string (YYYY-MM-DDTHH:mm)
            const year = end.getFullYear();
            const month = String(end.getMonth() + 1).padStart(2, '0');
            const day = String(end.getDate()).padStart(2, '0');
            const hours = String(end.getHours()).padStart(2, '0');
            const minutes = String(end.getMinutes()).padStart(2, '0');

            return `${year}-${month}-${day}T${hours}:${minutes}`;
        }

        // Hàm tính start time dựa trên end time và duration
        function calculateStartTime(endDateTime, durationHours) {
            if (!endDateTime || !durationHours || durationHours <= 0) return '';

            const end = new Date(endDateTime);
            const start = new Date(end.getTime() - (durationHours * 60 * 60 * 1000));

            // Format thành time string (HH:mm)
            const hours = String(start.getHours()).padStart(2, '0');
            const minutes = String(start.getMinutes()).padStart(2, '0');

            return `${hours}:${minutes}`;
        }

        // Các hàm cập nhật cho Add Form
        function updateAddFormDuration() {
            const bookingDate = document.querySelector('input[name="booking_date"]').value;
            const startTime = document.querySelector('input[name="start_time"]').value;
            const endTime = document.querySelector('input[name="end_time"]').value;
            const durationInput = document.querySelector('input[name="duration"]');

            if (bookingDate && startTime && endTime) {
                const startDateTime = `${bookingDate}T${startTime}`;
                const duration = calculateDuration(startDateTime, endTime);
                durationInput.value = duration;
            }
        }

        function updateAddFormEndTime() {
            const bookingDate = document.querySelector('input[name="booking_date"]').value;
            const startTime = document.querySelector('input[name="start_time"]').value;
            const duration = parseFloat(document.querySelector('input[name="duration"]').value);
            const endTimeInput = document.querySelector('input[name="end_time"]');

            if (bookingDate && startTime && duration > 0) {
                const startDateTime = `${bookingDate}T${startTime}`;
                const endTime = calculateEndTime(startDateTime, duration);
                endTimeInput.value = endTime;
            }
        }

        function updateAddFormStartTime() {
            const bookingDate = document.querySelector('input[name="booking_date"]').value;
            const endTime = document.querySelector('input[name="end_time"]').value;
            const duration = parseFloat(document.querySelector('input[name="duration"]').value);
            const startTimeInput = document.querySelector('input[name="start_time"]');

            if (bookingDate && endTime && duration > 0) {
                const startTime = calculateStartTime(endTime, duration);
                startTimeInput.value = startTime;
            }
        }

        // Các hàm cập nhật cho Edit Form
        function updateEditFormDuration() {
            const bookingDate = document.getElementById('edit-booking-date').value;
            const startTime = document.getElementById('edit-booking-start').value;
            const endTime = document.getElementById('edit-booking-endtime').value;
            const durationInput = document.getElementById('edit-booking-duration');

            if (bookingDate && startTime && endTime) {
                const startDateTime = `${bookingDate}T${startTime}`;
                const duration = calculateDuration(startDateTime, endTime);
                durationInput.value = duration;
            }
        }

        function updateEditFormEndTime() {
            const bookingDate = document.getElementById('edit-booking-date').value;
            const startTime = document.getElementById('edit-booking-start').value;
            const duration = parseFloat(document.getElementById('edit-booking-duration').value);
            const endTimeInput = document.getElementById('edit-booking-endtime');

            if (bookingDate && startTime && duration > 0) {
                const startDateTime = `${bookingDate}T${startTime}`;
                const endTime = calculateEndTime(startDateTime, duration);
                endTimeInput.value = endTime;
            }
        }

        function updateEditFormStartTime() {
            const bookingDate = document.getElementById('edit-booking-date').value;
            const endTime = document.getElementById('edit-booking-endtime').value;
            const duration = parseFloat(document.getElementById('edit-booking-duration').value);
            const startTimeInput = document.getElementById('edit-booking-start');

            if (bookingDate && endTime && duration > 0) {
                const startTime = calculateStartTime(endTime, duration);
                startTimeInput.value = startTime;
            }
        }

        // Hàm helper để format thời gian hiển thị
        function formatDuration(hours) {
            if (hours === 0) return "0 hours";

            const wholeHours = Math.floor(hours);
            const minutes = Math.round((hours - wholeHours) * 60);

            if (minutes === 0) {
                return `${wholeHours} hours`;
            } else {
                return `${wholeHours} hours ${minutes} minutes`;
            }
        }

        // Hàm validation để đảm bảo end time sau start time
        function validateTimeRange(startDateTime, endDateTime) {
            if (!startDateTime || !endDateTime) return true;

            const start = new Date(startDateTime);
            const end = new Date(endDateTime);

            return end > start;
        }

        // Hàm khởi tạo Add Form listeners
        function initAddFormListeners() {
            const addBookingDate = document.querySelector('input[name="booking_date"]');
            const addStartTime = document.querySelector('input[name="start_time"]');
            const addEndTime = document.querySelector('input[name="end_time"]');
            const addDuration = document.querySelector('input[name="duration"]');

            if (addBookingDate) {
                addBookingDate.removeEventListener('change', addBookingDateHandler);
                addBookingDate.addEventListener('change', addBookingDateHandler);
            }

            if (addStartTime) {
                addStartTime.removeEventListener('change', addStartTimeHandler);
                addStartTime.addEventListener('change', addStartTimeHandler);
            }

            if (addEndTime) {
                addEndTime.removeEventListener('change', addEndTimeHandler);
                addEndTime.addEventListener('change', addEndTimeHandler);
            }

            if (addDuration) {
                addDuration.removeEventListener('input', addDurationHandler);
                addDuration.addEventListener('input', addDurationHandler);
            }
        }

        // Các handler functions cho Add form
        function addBookingDateHandler() {
            updateAddFormEndTime();
        }

        function addStartTimeHandler() {
            const addDuration = document.querySelector('input[name="duration"]');
            const addEndTime = document.querySelector('input[name="end_time"]');
            const duration = parseFloat(addDuration.value);
            const endTime = addEndTime.value;

            if (duration > 0) {
                updateAddFormEndTime();
            } else if (endTime) {
                updateAddFormDuration();
            }
        }

        function addEndTimeHandler() {
            const addDuration = document.querySelector('input[name="duration"]');
            const addStartTime = document.querySelector('input[name="start_time"]');
            const duration = parseFloat(addDuration.value);
            const startTime = addStartTime.value;

            if (duration > 0 && !startTime) {
                updateAddFormStartTime();
            } else if (startTime) {
                updateAddFormDuration();
            }
        }

        function addDurationHandler() {
            const addStartTime = document.querySelector('input[name="start_time"]');
            const addEndTime = document.querySelector('input[name="end_time"]');
            const startTime = addStartTime.value;
            const endTime = addEndTime.value;

            if (startTime) {
                updateAddFormEndTime();
            } else if (endTime) {
                updateAddFormStartTime();
            }
        }

        function initializeDropdownDefaults() {
            // Lưu giá trị ban đầu cho tất cả dropdown để có thể khôi phục khi cần
            document.querySelectorAll('.status-dropdown, .payment-status-dropdown, .payment-method-dropdown').forEach(select => {
                select.dataset.oldValue = select.value;
                select.defaultValue = select.value;
            });
        }

        // Khởi tạo event listeners khi DOM loaded
        document.addEventListener('DOMContentLoaded', function() {
            initializeDropdownDefaults();
            // Khởi tạo trạng thái dropdown khi trang load
            updateDropdownStates();

            // Khởi tạo Add Form listeners ngay lập tức
            initAddFormListeners();

            // Khởi tạo lại Add Form listeners khi modal được mở
            const addBookingModal = document.getElementById('addBookingModal');
            if (addBookingModal) {
                addBookingModal.addEventListener('shown.bs.modal', function() {
                    initAddFormListeners();
                });
            }

            // Khởi tạo event listeners cho Add Booking button
            const addBookingBtn = document.querySelector('[data-bs-target="#addBookingModal"]');
            if (addBookingBtn) {
                addBookingBtn.addEventListener('click', function() {
                    // Reset form
                    document.getElementById('addBookingForm').reset();

                    // Đợi một chút để modal render xong rồi mới khởi tạo listeners
                    setTimeout(function() {
                        initAddFormListeners();
                    }, 100);
                });
            }

            // Edit Booking Form listeners
            const editBookingDate = document.getElementById('edit-booking-date');
            const editStartTime = document.getElementById('edit-booking-start');
            const editEndTime = document.getElementById('edit-booking-endtime');
            const editDuration = document.getElementById('edit-booking-duration');

            if (editBookingDate) {
                editBookingDate.addEventListener('change', function() {
                    updateEditFormEndTime(); // Cập nhật end time khi thay đổi date
                });
            }

            if (editStartTime) {
                editStartTime.addEventListener('change', function() {
                    // Nếu có duration, tính end time; nếu có end time, tính duration
                    const duration = parseFloat(editDuration.value);
                    const endTime = editEndTime.value;

                    if (duration > 0) {
                        updateEditFormEndTime();
                    } else if (endTime) {
                        updateEditFormDuration();
                    }
                });
            }

            if (editEndTime) {
                editEndTime.addEventListener('change', function() {
                    // Nếu có duration, tính start time; nếu có start time, tính duration
                    const duration = parseFloat(editDuration.value);
                    const startTime = editStartTime.value;

                    if (duration > 0 && !startTime) {
                        updateEditFormStartTime();
                    } else if (startTime) {
                        updateEditFormDuration();
                    }
                });
            }

            if (editDuration) {
                editDuration.addEventListener('input', function() {
                    // Nếu có start time, tính end time; nếu có end time nhưng không có start time, tính start time
                    const startTime = editStartTime.value;
                    const endTime = editEndTime.value;

                    if (startTime) {
                        updateEditFormEndTime();
                    } else if (endTime) {
                        updateEditFormStartTime();
                    }
                });
            }

            // Time validation for all time inputs
            document.querySelectorAll('input[type="time"]').forEach(input => {
                input.addEventListener('change', function() {
                    const time = this.value;
                    if (time && (time < '08:00' || time > '22:00')) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Invalid Time!',
                            text: 'Please select a time between 08:00 and 22:00.',
                            confirmButtonColor: '#ffc107'
                        });
                        this.value = '';
                    }
                });
            });
        });

        // Status dropdown change handler với kiểm soát các dropdown khác
        document.querySelectorAll('.status-dropdown').forEach(select => {
            select.addEventListener('change', function() {
                const bookingId = this.dataset.id;
                const newStatus = this.value;
                const oldStatus = this.dataset.oldValue || this.defaultValue; // Lưu giá trị cũ

                // **KIỂM TRA: Nếu status cũ đã là completed hoặc cancelled**
                if (oldStatus === 'completed' || oldStatus === 'cancelled') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Action Not Allowed!',
                        text: 'Cannot change status of completed or cancelled bookings!',
                        confirmButtonColor: '#dc3545'
                    });
                    this.value = oldStatus; // Khôi phục giá trị cũ
                    return;
                }

                // Lưu giá trị cũ trước khi thay đổi
                this.dataset.oldValue = oldStatus;

                fetch(`/admin/booking/${bookingId}/status`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            status: newStatus
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Booking status has been updated successfully.',
                                confirmButtonColor: '#198754',
                                timer: 2000,
                                timerProgressBar: true
                            });
                            // Cập nhật lại trạng thái các dropdown sau khi thay đổi status
                            updateDropdownStates();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Update Failed!',
                                text: 'Failed to update booking status.',
                                confirmButtonColor: '#dc3545'
                            });
                            this.value = oldStatus; // Khôi phục giá trị cũ nếu lỗi
                        }
                    })
                    .catch(err => {
                        console.error('Error:', err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'An error occurred while updating status.',
                            confirmButtonColor: '#dc3545'
                        });
                        this.value = oldStatus; // Khôi phục giá trị cũ nếu lỗi
                    });
            });
        });

        // Payment status dropdown với kiểm tra trạng thái
        document.querySelectorAll('.payment-status-dropdown').forEach(select => {
            select.addEventListener('change', function() {
                const bookingId = this.dataset.id;
                const newPaymentStatus = this.value;
                const oldPaymentStatus = this.dataset.oldValue || this.defaultValue;

                // **KIỂM TRA: Nếu đã thanh toán (paid) thì không được đổi**
                if (oldPaymentStatus === 'paid') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Action Not Allowed!',
                        text: 'Cannot change completed payment status!',
                        confirmButtonColor: '#dc3545'
                    });
                    this.value = oldPaymentStatus; // Khôi phục giá trị cũ
                    return;
                }

                // Kiểm tra disable (logic cũ vẫn giữ)
                if (this.disabled) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Not Available!',
                        text: 'Cannot change payment status when booking is not in pending status!',
                        confirmButtonColor: '#ffc107'
                    });
                    return;
                }

                // Lưu giá trị cũ
                this.dataset.oldValue = oldPaymentStatus;

                fetch(`/admin/booking/${bookingId}/payment-status`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            payment_status: newPaymentStatus
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Payment status has been updated successfully.',
                                confirmButtonColor: '#198754',
                                timer: 2000,
                                timerProgressBar: true
                            });
                            // Cập nhật lại trạng thái các dropdown
                            updateDropdownStates();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Update Failed!',
                                text: 'Failed to update payment status.',
                                confirmButtonColor: '#dc3545'
                            });
                            this.value = oldPaymentStatus; // Khôi phục nếu lỗi
                        }
                    })
                    .catch(err => {
                        console.error('Error:', err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'An error occurred while updating payment status.',
                            confirmButtonColor: '#dc3545'
                        });
                        this.value = oldPaymentStatus; // Khôi phục nếu lỗi
                    });
            });
        });

        // Payment method dropdown với kiểm tra trạng thái
        document.querySelectorAll('.payment-method-dropdown').forEach(select => {
            select.addEventListener('change', function() {
                const bookingId = this.dataset.id;
                const newPaymentMethod = this.value;
                const oldPaymentMethod = this.dataset.oldValue || this.defaultValue;

                // Lấy payment_status hiện tại
                const paymentStatusDropdown = document.querySelector(
                    `.payment-status-dropdown[data-id="${bookingId}"]`);
                const currentPaymentStatus = paymentStatusDropdown ? paymentStatusDropdown.value : '';

                // **KIỂM TRA: Nếu đã thanh toán VÀ phương thức cũ là balance**
                if (currentPaymentStatus === 'paid' && oldPaymentMethod === 'balance') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Action Not Allowed!',
                        text: 'Cannot change completed balance payment method!',
                        confirmButtonColor: '#dc3545'
                    });
                    this.value = oldPaymentMethod; // Khôi phục giá trị cũ
                    return;
                }

                // Kiểm tra disable (logic cũ vẫn giữ)
                if (this.disabled) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Not Available!',
                        text: 'Cannot change payment method when booking is not in pending status!',
                        confirmButtonColor: '#ffc107'
                    });
                    return;
                }

                // Lưu giá trị cũ
                this.dataset.oldValue = oldPaymentMethod;

                fetch(`/admin/booking/${bookingId}/payment-method`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            payment_method: newPaymentMethod
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Payment method has been updated successfully.',
                                confirmButtonColor: '#198754',
                                timer: 2000,
                                timerProgressBar: true
                            });
                            // Cập nhật lại trạng thái các dropdown
                            updateDropdownStates();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Update Failed!',
                                text: 'Failed to update payment method.',
                                confirmButtonColor: '#dc3545'
                            });
                            this.value = oldPaymentMethod; // Khôi phục nếu lỗi
                        }
                    })
                    .catch(err => {
                        console.error('Error:', err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'An error occurred while updating payment method.',
                            confirmButtonColor: '#dc3545'
                        });
                        this.value = oldPaymentMethod; // Khôi phục nếu lỗi
                    });
            });
        });

        function deleteBooking(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this action!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/admin/booking/${id}/delete`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(res => res.json())
                        .then((data) => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Booking has been deleted successfully.',
                                    confirmButtonColor: '#198754'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Delete Failed!',
                                    text: 'Failed to delete the booking.',
                                    confirmButtonColor: '#dc3545'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'An error occurred while deleting the booking.',
                                confirmButtonColor: '#dc3545'
                            });
                        });
                }
            });
        }

        // Add booking form submission with validation
        document.getElementById('addBookingForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Validate time range
            const bookingDate = document.querySelector('input[name="booking_date"]').value;
            const startTime = document.querySelector('input[name="start_time"]').value;
            const endTime = document.querySelector('input[name="end_time"]').value;

            if (bookingDate && startTime && endTime) {
                const startDateTime = `${bookingDate}T${startTime}`;

                if (!validateTimeRange(startDateTime, endTime)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid Time Range!',
                        text: 'End time must be after start time!',
                        confirmButtonColor: '#ffc107'
                    });
                    return false;
                }
            }

            let form = e.target;
            let formData = new FormData(form);

            // Show loading
            Swal.fire({
                title: 'Creating Booking...',
                text: 'Please wait while we create the booking.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(async (res) => {
                    const data = await res.json();
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: data.message || 'Booking created successfully.',
                            confirmButtonColor: '#198754'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        if (data.errors) {
                            let errorMessages = Object.values(data.errors).flat();
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error!',
                                text: errorMessages.join(', '),
                                confirmButtonColor: '#dc3545'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: data.message || 'An error occurred while creating the booking.',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    }
                })
                .catch(err => {
                    if (err.errors) {
                        let errorMessages = Object.values(err.errors).flat();
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error!',
                            text: errorMessages.join(', '),
                            confirmButtonColor: '#dc3545'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: err.message || 'An error occurred while creating the booking.',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                });
        });

        // Cải thiện phần xử lý edit booking
        document.querySelectorAll('.editBookingBtn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Lấy dữ liệu
                const id = this.dataset.id;
                const userId = this.dataset.user_id;
                const roomId = this.dataset.room_id;

                // Lấy tên phòng từ cột Room trong table (cột thứ 3)
                const roomName = this.closest('tr').querySelector('td:nth-child(3)').textContent.trim();

                const date = this.dataset.date;
                const start = this.dataset.start;
                const duration = this.dataset.duration;
                const notes = this.dataset.notes;
                // Lấy endtime từ dòng data-end (nếu có)
                const endtime = this.dataset.end ? this.dataset.end : '';

                // Đổ dữ liệu vào form
                document.getElementById('edit-booking-id').value = id;
                document.getElementById('editBookingForm').action = `/admin/booking/${id}/update`;
                document.getElementById('edit-booking-date').value = date;
                document.getElementById('edit-booking-endtime').value = endtime;
                document.getElementById('edit-booking-start').value = start;
                document.getElementById('edit-booking-user').value = userId;
                document.getElementById('edit-booking-duration').value = parseInt(duration, 10);
                document.getElementById('edit-booking-notes').value = notes;

                // Hiển thị tên phòng và disable input
                document.getElementById('edit-booking-room-display').value = roomName;
                document.getElementById('edit-booking-room-id').value = roomId;

                // Đảm bảo input hiển thị room name bị disable
                const roomDisplayInput = document.getElementById('edit-booking-room-display');
                roomDisplayInput.disabled = true;
                roomDisplayInput.style.backgroundColor = '#f8f9fa';
                roomDisplayInput.style.color = '#6c757d';
                roomDisplayInput.style.cursor = 'not-allowed';

                // Hiện modal
                const modal = new bootstrap.Modal(document.getElementById('editBookingModal'));
                modal.show();
            });
        });

        // Edit booking form submission with validation
        document.getElementById('editBookingForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Validate time range
            const bookingDate = document.getElementById('edit-booking-date').value;
            const startTime = document.getElementById('edit-booking-start').value;
            const endTime = document.getElementById('edit-booking-endtime').value;

            if (bookingDate && startTime && endTime) {
                const startDateTime = `${bookingDate}T${startTime}`;

                if (!validateTimeRange(startDateTime, endTime)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid Time Range!',
                        text: 'End time must be after start time!',
                        confirmButtonColor: '#ffc107'
                    });
                    return false;
                }
            }

            let form = e.target;
            let formData = new FormData(form);
            let bookingId = form.querySelector('input[name="booking_id"]').value;

            // Show loading
            Swal.fire({
                title: 'Updating Booking...',
                text: 'Please wait while we update the booking.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`/admin/booking/${bookingId}/update`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(async res => {
                    const data = await res.json();
                    if (!res.ok) throw data;

                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message || 'Booking updated successfully.',
                        confirmButtonColor: '#198754'
                    }).then(() => {
                        location.reload();
                    });
                })
                .catch(err => {
                    if (err.errors) {
                        let errorMessages = Object.values(err.errors).flat();
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error!',
                            text: errorMessages.join(', '),
                            confirmButtonColor: '#dc3545'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: err.message || 'An error occurred while updating the booking.',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                });
        });

        // Xem chi tiết booking
        document.querySelectorAll('.viewBookingBtn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('detail-id').innerText = this.dataset.id;
                document.getElementById('detail-user').innerText = this.dataset.user;
                document.getElementById('detail-useremail').innerText = this.dataset.useremail;
                document.getElementById('detail-room').innerText = this.dataset.room;
                document.getElementById('detail-start').innerText = this.dataset.start;
                document.getElementById('detail-end').innerText = this.dataset.end;
                document.getElementById('detail-hours').innerText = this.dataset.hours;
                document.getElementById('detail-payment_method').innerText = this.dataset.payment_method;
                document.getElementById('detail-payment_status').innerText = this.dataset.payment_status;
                document.getElementById('detail-status').innerText = this.dataset.status;
                document.getElementById('detail-total').innerText = this.dataset.total;
                document.getElementById('detail-notes').innerText = this.dataset.notes;

                const modal = new bootstrap.Modal(document.getElementById('viewBookingModal'));
                modal.show();
            });
        });
    </script>
@endsection
