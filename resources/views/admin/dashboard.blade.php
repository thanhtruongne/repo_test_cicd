@extends('layouts.admin')

@section('title', 'Admin Panel - Dashboard')

@section('dashboard-active', 'active')

@section('page-title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Home</a></li>
    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
@endsection

@section('styles')
    {{-- ...existing code... --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <style>
        /* CSS tập trung vào booking events để dễ nhìn hơn */
        
        /* Booking Event Container */
        .fc-event {
            border: none !important;
            border-radius: 6px !important;
            margin: 1px 2px !important;
            padding: 0 !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
            overflow: hidden !important;
            font-size: 11px !important;
            min-height: 22px !important;
        }

        /* Booking Content */
        .fc-event-main {
            padding: 3px 6px !important;
            height: 100% !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
        }

        .fc-event-title {
            font-weight: 600 !important;
            color: #2c3e50 !important;
            line-height: 1.2 !important;
            margin: 0 !important;
            font-size: 11px !important;
            text-overflow: ellipsis !important;
            overflow: hidden !important;
            white-space: nowrap !important;
        }

        /* Hiển thị thời gian booking */
        .fc-event-time {
            display: block !important;
            font-size: 9px !important;
            color: #666 !important;
            font-weight: 500 !important;
            margin-top: 1px !important;
            line-height: 1.1 !important;
        }

        /* Container cho thông tin booking */
        .booking-details {
            display: flex !important;
            flex-direction: column !important;
            gap: 1px !important;
        }

        /* Thông tin thời gian và thời lượng */
        .booking-time-info {
            font-size: 9px !important;
            color: #555 !important;
            font-weight: 500 !important;
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
        }

        .booking-duration {
            background: rgba(255, 255, 255, 0.8) !important;
            padding: 1px 4px !important;
            border-radius: 3px !important;
            font-weight: 600 !important;
            font-size: 8px !important;
        }

        /* Màu sắc dễ phân biệt cho booking */
        .fc-event {
            background: #e3f2fd !important; /* Xanh nhạt làm nền chính */
            border-left: 4px solid #2196f3 !important;
        }

        /* Khi hover vào booking */
        .fc-event:hover {
            background: #bbdefb !important;
            transform: scale(1.02) !important;
            transition: all 0.2s ease !important;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15) !important;
            z-index: 10 !important;
        }

        .fc-event:hover .fc-event-title {
            color: #1565c0 !important;
            font-weight: 700 !important;
        }

        /* Màu khác nhau cho các loại booking */
        
        /* Admin booking - Màu cam */
        .fc-event.admin-booking {
            background: #fff3e0 !important;
            border-left-color: #ff9800 !important;
        }
        .fc-event.admin-booking:hover {
            background: #ffe0b2 !important;
        }
        .fc-event.admin-booking .fc-event-title {
            color: #ef6c00 !important;
        }

        /* User booking thường - Màu xanh */
        .fc-event.user-booking {
            background: #e8f5e8 !important;
            border-left-color: #4caf50 !important;
        }
        .fc-event.user-booking:hover {
            background: #c8e6c9 !important;
        }
        .fc-event.user-booking .fc-event-title {
            color: #2e7d32 !important;
        }

        /* Pending booking - Màu vàng */
        .fc-event.pending-booking {
            background: #fffde7 !important;
            border-left-color: #ffc107 !important;
        }
        .fc-event.pending-booking:hover {
            background: #fff9c4 !important;
        }
        .fc-event.pending-booking .fc-event-title {
            color: #f57f17 !important;
        }

        /* VIP booking - Màu tím */
        .fc-event.vip-booking {
            background: #f3e5f5 !important;
            border-left-color: #9c27b0 !important;
        }
        .fc-event.vip-booking:hover {
            background: #e1bee7 !important;
        }
        .fc-event.vip-booking .fc-event-title {
            color: #6a1b9a !important;
        }

        /* Cancelled booking - Màu đỏ nhạt */
        .fc-event.cancelled-booking {
            background: #ffebee !important;
            border-left-color: #f44336 !important;
            opacity: 0.7 !important;
        }
        .fc-event.cancelled-booking:hover {
            background: #ffcdd2 !important;
        }
        .fc-event.cancelled-booking .fc-event-title {
            color: #c62828 !important;
            text-decoration: line-through !important;
        }

        /* Thêm thông tin chi tiết cho booking */
        .booking-info {
            font-size: 9px !important;
            color: #666 !important;
            margin-top: 1px !important;
            line-height: 1.1 !important;
        }

        /* Tooltip chi tiết khi hover */
        .fc-event {
            position: relative !important;
        }

        .fc-event::after {
            content: attr(data-tooltip) !important;
            position: absolute !important;
            bottom: 100% !important;
            left: 50% !important;
            transform: translateX(-50%) !important;
            background: rgba(0, 0, 0, 0.9) !important;
            color: white !important;
            padding: 8px 12px !important;
            border-radius: 4px !important;
            font-size: 12px !important;
            white-space: nowrap !important;
            opacity: 0 !important;
            visibility: hidden !important;
            transition: all 0.3s ease !important;
            z-index: 1000 !important;
            pointer-events: none !important;
        }

        .fc-event:hover::after {
            opacity: 1 !important;
            visibility: visible !important;
        }

        /* Icon cho các loại booking */
        .booking-icon {
            display: inline-block !important;
            width: 12px !important;
            height: 12px !important;
            border-radius: 50% !important;
            margin-right: 4px !important;
            vertical-align: middle !important;
        }

        .admin-booking .booking-icon {
            background: #ff9800 !important;
        }

        .user-booking .booking-icon {
            background: #4caf50 !important;
        }

        .pending-booking .booking-icon {
            background: #ffc107 !important;
        }

        .vip-booking .booking-icon {
            background: #9c27b0 !important;
        }

        .cancelled-booking .booking-icon {
            background: #f44336 !important;
        }

        /* Responsive cho mobile */
        @media (max-width: 768px) {
            .fc-event {
                min-height: 20px !important;
                font-size: 10px !important;
            }
            
            .fc-event-title {
                font-size: 10px !important;
            }
            
            .booking-info {
                font-size: 8px !important;
            }
            
            .booking-icon {
                width: 10px !important;
                height: 10px !important;
            }

            .booking-time-info {
                font-size: 8px !important;
            }

            .booking-duration {
                font-size: 7px !important;
            }
        }

        /* Cải thiện thời gian slots */
        .fc-timegrid-slot {
            height: 40px !important;
            border-color: #f0f0f0 !important;
        }

        .fc-timegrid-slot-minor {
            border-color: #f8f8f8 !important;
        }

        /* Làm nổi bật giờ làm việc */
        .fc-timegrid-slot[data-time^="08"],
        .fc-timegrid-slot[data-time^="09"],
        .fc-timegrid-slot[data-time^="10"],
        .fc-timegrid-slot[data-time^="11"],
        .fc-timegrid-slot[data-time^="13"],
        .fc-timegrid-slot[data-time^="14"],
        .fc-timegrid-slot[data-time^="15"],
        .fc-timegrid-slot[data-time^="16"],
        .fc-timegrid-slot[data-time^="17"] {
            background-color: rgba(129, 199, 132, 0.03) !important;
        }

        /* Làm mờ giờ nghỉ trưa */
        .fc-timegrid-slot[data-time^="12"] {
            background-color: rgba(255, 193, 7, 0.05) !important;
        }

        /* Làm mờ giờ ngoài giờ */
        .fc-timegrid-slot[data-time^="18"],
        .fc-timegrid-slot[data-time^="19"],
        .fc-timegrid-slot[data-time^="20"],
        .fc-timegrid-slot[data-time^="21"] {
            background-color: rgba(158, 158, 158, 0.03) !important;
        }
    </style>
@endsection

@section('content')
    @if (session('status'))
        <div class="alert alert-success mb-3">
            {{ session('status') }}
        </div>
    @endif
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="stats-card primary">
                <div class="stats-icon primary">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="mb-1">{{$totalUsers}}</h3>
                <p class="text-muted mb-0">Total Users</p>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="stats-card success">
                <div class="stats-icon success">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="mb-1">{{$totalRooms}}</h3>
                <p class="text-muted mb-0">Rooms</p>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="stats-card warning">
                <div class="stats-icon warning">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h3 class="mb-1">{{$totalBookings}}</h3>
                <p class="text-muted mb-0">Booking</p>
            </div>
        </div>
        {{-- <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="stats-card danger">
                <div class="stats-icon danger">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 class="mb-1">23</h3>
                <p class="text-muted mb-0">Issues</p>
            </div>
        </div> --}}
    </div>

    <div class="main-content">
        <div class="row mt-4">
            <div class="col-12">
                <h4 class="mb-3">Lịch đặt phòng</h4>
                <div id="calendar"></div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        // Function to calculate duration in hours
        function calculateDuration(start, end) {
            const startTime = new Date(start);
            const endTime = new Date(end);
            const diffMs = endTime - startTime;
            const diffHours = diffMs / (1000 * 60 * 60);
            
            if (diffHours < 1) {
                const diffMinutes = Math.round(diffMs / (1000 * 60));
                return diffMinutes + 'p'; // p for phút (minutes)
            } else if (diffHours === 1) {
                return '1h';
            } else if (diffHours < 24) {
                const hours = Math.floor(diffHours);
                const minutes = Math.round((diffHours - hours) * 60);
                if (minutes === 0) {
                    return hours + 'h';
                } else {
                    return hours + 'h' + minutes + 'p';
                }
            } else {
                const days = Math.floor(diffHours / 24);
                const remainingHours = Math.floor(diffHours % 24);
                if (remainingHours === 0) {
                    return days + 'd';
                } else {
                    return days + 'd' + remainingHours + 'h';
                }
            }
        }

        // Function to format time
        function formatTime(dateTime) {
            // Sử dụng cách format đơn giản hơn để tránh timezone issues
            const date = new Date(dateTime);
            const hours = date.getHours().toString().padStart(2, '0');
            const minutes = date.getMinutes().toString().padStart(2, '0');
            return `${hours}:${minutes}`;
        }

        // Initialize Calendar
        var calendarEl = document.getElementById('calendar');
        if (calendarEl) {
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
                timeZone: 'local', // Sử dụng timezone local thay vì UTC
                // Sử dụng API lấy tất cả booking của tất cả phòng cho admin
                events: {
                    url: '/admin/api/all-bookings',
                    success: function(data) {
                        // console.log('API Response:', data); // Debug để xem dữ liệu API
                    },
                    failure: function() {
                        // console.log('Failed to load events');
                    }
                },
                eventColor: '#d1992c',
                eventDisplay: 'block',
                height: 'auto',
                eventContent: function(arg) {
                    // Debug để xem dữ liệu event
                    // console.log('Event data:', arg.event);
                    // console.log('Extended props:', arg.event.extendedProps);
                    
                    // Tính toán thời lượng
                    const duration = calculateDuration(arg.event.start, arg.event.end);
                    const startTime = formatTime(arg.event.start);
                    const endTime = formatTime(arg.event.end);
                    
                    // Thử nhiều cách lấy thông tin khách hàng và phòng
                    const customerName = arg.event.extendedProps.customer_name || 
                                       arg.event.extendedProps.user_name || 
                                       arg.event.extendedProps.name || 
                                       arg.event.title || 'Khách hàng';
                    
                    const roomName = arg.event.extendedProps.room_name || 
                                   arg.event.extendedProps.room || 
                                   arg.event.extendedProps.room_title || 'Phòng';
                    
                    const bookingType = arg.event.extendedProps.booking_type || 'standard';
                    
                    // Tạo tooltip với thông tin chi tiết
                    const tooltipText = `${customerName} - ${roomName}\\nThời gian: ${startTime} - ${endTime}\\nThời lượng: ${duration}`;
                    
                    // Hiển thị tên người đặt, phòng và thời lượng
                    return {
                        html: '<div class="fc-event-main-frame" data-tooltip="' + tooltipText + '">' +
                              '<div class="booking-details">' +
                                '<div class="fc-event-title">' + 
                                  '<span class="booking-icon"></span>' +
                                  customerName + 
                                '</div>' +
                                '<div class="booking-time-info">' +
                                  '<span class="booking-time">' + startTime + ' - ' + endTime + '</span>' +
                                  '<span class="booking-duration">' + duration + '</span>' +
                                '</div>' +
                                '<div class="booking-info">' + roomName + '</div>' +
                              '</div>' +
                              '</div>'
                    };
                },
                eventDidMount: function(info) {
                    // Thêm class cho các loại booking khác nhau
                    const bookingType = info.event.extendedProps.booking_type;
                    const status = info.event.extendedProps.status;
                    
                    if (status === 'cancelled') {
                        info.el.classList.add('cancelled-booking');
                    } else if (bookingType === 'admin') {
                        info.el.classList.add('admin-booking');
                    } else if (bookingType === 'vip') {
                        info.el.classList.add('vip-booking');
                    } else if (status === 'pending') {
                        info.el.classList.add('pending-booking');
                    } else {
                        info.el.classList.add('user-booking');
                    }
                },
                eventClick: function(info) {
                    // Debug để xem tất cả dữ liệu
                    // console.log('Full event object:', info.event);
                    // console.log('All extended props:', info.event.extendedProps);
                    
                    // Hiển thị thông tin chi tiết khi click
                    const event = info.event;
                    const duration = calculateDuration(event.start, event.end);
                    const startTime = formatTime(event.start);
                    const endTime = formatTime(event.end);
                    
                    // Thử nhiều cách lấy tên khách hàng
                    const customerName = event.extendedProps.customer_name || 
                                       event.extendedProps.user_name || 
                                       event.extendedProps.name ||
                                       event.extendedProps.customer ||
                                       event.title || 'Chưa có tên';
                    
                    // Thử nhiều cách lấy tên phòng
                    const roomName = event.extendedProps.room_name || 
                                   event.extendedProps.room || 
                                   event.extendedProps.room_title ||
                                   event.extendedProps.room_number || 'Chưa có phòng';
                    
                    alert(
                        'Chi tiết đặt phòng:\n\n' +
                        'Khách hàng: ' + customerName + '\n' +
                        'Phòng: ' + roomName + '\n' +
                        'Thời gian: ' + startTime + ' - ' + endTime + '\n' +
                        'Thời lượng: ' + duration + '\n' +
                        'Trạng thái: ' + (event.extendedProps.status || 'N/A') + '\n\n' 
                        // 'Debug - Tất cả extendedProps:\n' + JSON.stringify(event.extendedProps, null, 2)
                    );
                }
            });
            calendar.render();
        }

        // Initialize Flatpickr for booking date
        flatpickr('input[name="booking_date"]', {
            minDate: 'today',
            dateFormat: 'Y-m-d',
            locale: {
                firstDayOfWeek: 1
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

        // Nếu có tính tổng tiền, bạn có thể thêm logic tương tự ở đây nếu cần
      });
    </script>
@endsection