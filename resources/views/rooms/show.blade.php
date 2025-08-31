@extends('layouts.app')

@section('title', $room->name)

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css">
@endsection

@section('content')
    <div class="container py-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('rooms.index') }}">Phòng thu</a></li>
                <li class="breadcrumb-item active">{{ $room->name }}</li>
            </ol>
        </nav>

        <div class="row">
            <!-- Room Info -->
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <!-- Room Image -->
                    <img src="{{ asset($room->image_url) }}" alt="{{ $room->name }}" class="card-img-top"
                        style="height: 400px; object-fit: cover;">

                    <div class="card-body">
                        <!-- Room Header -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h1 class="h2 mb-0">{{ $room->name }}</h1>
                            <span class="badge bg-primary">{{ number_format($room->hourly_rate) }}đ/giờ</span>
                        </div>

                        <!-- Room Description -->
                        <p class="lead mb-4">{{ $room->description }}</p>

                        <!-- Equipment List -->
                        <h5 class="mb-3">Trang thiết bị</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <i class="fas fa-microphone text-primary me-3"></i>
                                    <div>
                                        <h6 class="mb-0">Micro thu âm</h6>
                                        <small class="text-muted">Shure SM7B, Neumann TLM 103</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <i class="fas fa-headphones text-primary me-3"></i>
                                    <div>
                                        <h6 class="mb-0">Headphone</h6>
                                        <small class="text-muted">Audio-Technica ATH-M50x</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <i class="fas fa-sliders-h text-primary me-3"></i>
                                    <div>
                                        <h6 class="mb-0">Mixer</h6>
                                        <small class="text-muted">Focusrite Scarlett 18i20</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <i class="fas fa-laptop text-primary me-3"></i>
                                    <div>
                                        <h6 class="mb-0">DAW Software</h6>
                                        <small class="text-muted">Pro Tools, Logic Pro X</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Calendar -->
                        <h5 class="mb-3">Lịch đặt phòng</h5>
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>

            <!-- Booking Form -->
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Đặt phòng</h4>

                        @guest
                            <div class="alert alert-warning">
                                Vui lòng <a href="{{ route('login') }}" class="alert-link">đăng nhập</a> để đặt phòng.
                            </div>
                        @else
                            <form id="bookingForm" action="{{ route('bookings.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="room_id" value="{{ $room->id }}">

                                <!-- Date -->
                                <div class="mb-3">
                                    <label class="form-label">Ngày đặt</label>
                                    <input type="date" class="form-control" name="booking_date" min="{{ date('Y-m-d') }}"
                                        required>
                                </div>

                                <!-- Time -->
                                <div class="mb-3">
                                    <label class="form-label">Giờ bắt đầu</label>
                                    <input type="time" class="form-control" name="start_time" required>
                                </div>

                                <!-- Duration -->
                                <div class="mb-3">
                                    <label class="form-label">Thời gian thuê</label>
                                    <select class="form-select" name="duration" required>
                                        @for ($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}">{{ $i }} giờ</option>
                                        @endfor
                                    </select>
                                </div>

                                <!-- Payment Method -->
                                <div class="mb-3">
                                    <label class="form-label">Phương thức thanh toán</label>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="payment_method" id="cash"
                                            value="cash" checked>
                                        <label class="form-check-label" for="cash">
                                            <i class="fas fa-money-bill-wave text-success me-2"></i>
                                            Tiền mặt
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="transfer"
                                            value="bank_transfer">
                                        <label class="form-check-label" for="transfer">
                                            <i class="fas fa-university text-primary me-2"></i>
                                            Chuyển khoản
                                        </label>
                                    </div>
                                     <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="payment_method" id="balance"
                                            value="balance">
                                        <label class="form-check-label" for="balance">
                                           <i class="fa-solid fa-wallet me-2 text-success"></i>
                                            Thanh toán bằng số dư
                                        </label>
                                    </div>
                                </div>

                                <!-- Notes -->
                                <div class="mb-4">
                                    <label class="form-label">Ghi chú</label>
                                    <textarea class="form-control" name="notes" rows="3" placeholder="Nhập yêu cầu đặc biệt nếu có"></textarea>
                                </div>

                                <!-- Total -->
                                <div class="card bg-light mb-4">
                                    <div class="card-body">
                                        <h5 class="card-title">Tổng thanh toán</h5>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Giá theo giờ:</span>
                                            <span>{{ number_format($room->hourly_rate) }}đ</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Số giờ:</span>
                                            <span id="hours">1</span>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between">
                                            <strong>Tổng cộng:</strong>
                                            <strong class="text-primary" id="total">
                                                {{ number_format($room->hourly_rate) }}đ
                                            </strong>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-calendar-check me-2"></i>
                                    Xác nhận đặt phòng
                                </button>
                            </form>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
    // Initialize Calendar
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        slotMinTime: '08:00:00',
        slotMaxTime: '22:00:00',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'timeGridWeek,timeGridDay'
        },
        locale: 'vi',
        timeZone: 'UTC', // Important: match the timezone of the API response
        events: '/api/rooms/{{ $room->id }}/bookings',
        eventColor: '#d1992c',
        eventDisplay: 'block',
        height: 'auto',
        // Customize event rendering
        eventContent: function(arg) {
            return {
                html: '<div class="fc-event-main-frame">' +
                      '<div class="fc-event-title">Đã đặt</div>' +
                      '</div>'
            };
        }
    });
    calendar.render();

    // Initialize Flatpickr for booking date
    flatpickr('input[name="booking_date"]', {
        minDate: 'today',
        dateFormat: 'Y-m-d',
        locale: {
            firstDayOfWeek: 1 // Monday as first day
        }
    });

    // Initialize Flatpickr for start time
    flatpickr('input[name="start_time"]', {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        minTime: "08:00",
        maxTime: "22:00",
        time_24hr: true
    });

    // Calculate total
    function calculateTotal() {
        var hours = parseInt(document.querySelector('select[name="duration"]').value);
        var rate = {{ $room->hourly_rate }};
        var total = hours * rate;

        document.getElementById('hours').textContent = hours;
        document.getElementById('total').textContent = total.toLocaleString() + 'đ';
    }

    // Add event listener for duration change
    document.querySelector('select[name="duration"]').addEventListener('change', calculateTotal);

    // Initial calculation
    calculateTotal();
});
        // Thêm action route vào form
        document.addEventListener('DOMContentLoaded', function() {
            // Existing calendar and flatpickr initialization code...

            const bookingForm = document.getElementById('bookingForm');

            // Function to create and display error message
            function displayError(message) {
                // Remove any existing error alert
                const existingAlert = document.querySelector('.booking-error-alert');
                if (existingAlert) {
                    existingAlert.remove();
                }

                // Create error alert
                const errorAlert = document.createElement('div');
                errorAlert.className = 'alert alert-danger booking-error-alert mt-3';
                errorAlert.textContent = message;

                // Insert error alert before the submit button
                const submitButton = bookingForm.querySelector('button[type="submit"]');
                submitButton.parentElement.insertBefore(errorAlert, submitButton);

                // Scroll to the top of the form
                errorAlert.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }

            // Function to remove field-specific errors
            function clearFieldErrors() {
                const errorFields = bookingForm.querySelectorAll('.is-invalid');
                errorFields.forEach(field => {
                    field.classList.remove('is-invalid');
                });

                const existingErrorMessages = bookingForm.querySelectorAll('.invalid-feedback');
                existingErrorMessages.forEach(message => message.remove());
            }

            // Function to add field-specific error
            function addFieldError(fieldName, message) {
                const field = bookingForm.querySelector(`[name="${fieldName}"]`);
                if (field) {
                    field.classList.add('is-invalid');

                    // Create error message element
                    const errorMessage = document.createElement('div');
                    errorMessage.className = 'invalid-feedback d-block';
                    errorMessage.textContent = message;

                    // Insert after the input field
                    field.parentElement.appendChild(errorMessage);
                }
            }

            bookingForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Clear previous errors
                clearFieldErrors();
                const existingErrorAlert = document.querySelector('.booking-error-alert');
                if (existingErrorAlert) {
                    existingErrorAlert.remove();
                }

                // Basic client-side validation
                const bookingDate = bookingForm.querySelector('input[name="booking_date"]');
                const startTime = bookingForm.querySelector('input[name="start_time"]');
                const duration = bookingForm.querySelector('select[name="duration"]');

                let hasError = false;

                // Date validation
                if (!bookingDate.value) {
                    addFieldError('booking_date', 'Vui lòng chọn ngày đặt phòng');
                    hasError = true;
                }

                // Start time validation
                if (!startTime.value) {
                    addFieldError('start_time', 'Vui lòng chọn giờ bắt đầu');
                    hasError = true;
                }

                // Duration validation
                if (!duration.value) {
                    addFieldError('duration', 'Vui lòng chọn thời gian thuê');
                    hasError = true;
                }

                // If there are client-side errors, stop here
                if (hasError) {
                    return;
                }

                // Lấy thông tin để hiển thị trong modal
                const bookingDateValue = bookingDate.value;
                const startTimeValue = startTime.value;
                const durationValue = duration.value;
                const total = document.getElementById('total').textContent;
                const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
                const paymentText = paymentMethod == 'cash' ?  'Tiền mặt' : (paymentMethod == 'bank_transfer' ? 'Chuyển khoản' : "Thanh toán bằng số dư");
                // Xóa modal cũ nếu tồn tại
                const existingModal = document.getElementById('confirmBookingModal');
                if (existingModal) {
                    existingModal.remove();
                }

                // HTML cho modal
                const modalHtml = `
        <div class="modal fade" id="confirmBookingModal" tabindex="-1" aria-labelledby="confirmBookingModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmBookingModalLabel">Xác nhận đặt phòng</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Bạn có chắc chắn muốn đặt phòng với thông tin sau:</p>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>Ngày:</strong> ${bookingDateValue}</li>
                            <li class="list-group-item"><strong>Thời gian:</strong> ${startTimeValue}</li>
                            <li class="list-group-item"><strong>Số giờ:</strong> ${durationValue} giờ</li>
                            <li class="list-group-item"><strong>Thanh toán:</strong> ${paymentText}</li>
                            <li class="list-group-item"><strong>Tổng tiền:</strong> ${total}</li>
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="button" class="btn btn-primary" id="confirmBooking">Xác nhận đặt phòng</button>
                    </div>
                </div>
            </div>
        </div>`;

                // Thêm modal vào body
                document.body.insertAdjacentHTML('beforeend', modalHtml);

                // Khởi tạo và show modal
                const modalElement = document.getElementById('confirmBookingModal');
                const modal = new bootstrap.Modal(modalElement);
                modal.show();

                // Xử lý khi click nút xác nhận
                document.getElementById('confirmBooking').addEventListener('click', function() {
                    const formData = new FormData(bookingForm);

                    fetch(bookingForm.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.location.href = data.redirect;
                            } else {
                                // Close the confirmation modal
                                bootstrap.Modal.getInstance(modalElement).hide();

                                // Check if there are field-specific errors
                                if (data.errors) {
                                    Object.keys(data.errors).forEach(field => {
                                        addFieldError(field, data.errors[field][0]);
                                    });
                                }

                                // Display general error message if exists
                                if (data.message) {
                                    displayError(data.message);
                                }
                            }
                        })
                        .catch(error => {
                            // Close the confirmation modal
                            bootstrap.Modal.getInstance(modalElement).hide();

                            // Display a generic error message
                            displayError('Đã có lỗi xảy ra. Vui lòng thử lại sau.');
                            console.error('Error:', error);
                        });
                });

                // Xóa modal khi đóng
                modalElement.addEventListener('hidden.bs.modal', function() {
                    this.remove();
                });
            });
        });
    </script>
@endsection
