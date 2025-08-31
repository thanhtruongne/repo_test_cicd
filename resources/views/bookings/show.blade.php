@extends('layouts.app')

@section('title', 'Chi tiết đặt phòng #' . $booking->id)

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('bookings.index') }}">Đặt phòng</a></li>
            <li class="breadcrumb-item active">Chi tiết #{{ $booking->id }}</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h4 class="mb-0">Chi tiết đặt phòng #{{ $booking->id }}</h4>
                </div>

                <div class="card-body">
                    <!-- Thông tin phòng -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="mb-3">Thông tin phòng</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td class="ps-0"><strong>Phòng:</strong></td>
                                    <td>{{ $booking->room->name }}</td>
                                </tr>
                                <tr>
                                    <td class="ps-0"><strong>Thời gian:</strong></td>
                                    <td>
                                        {{ $booking->start_time->format('H:i d/m/Y') }} -
                                        {{ $booking->end_time->format('H:i') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="ps-0"><strong>Số giờ:</strong></td>
                                    <td>{{ $booking->total_hours }} giờ</td>
                                </tr>
                                <tr>
                                    <td class="ps-0"><strong>Trạng thái:</strong></td>
                                    <td>
                                        @switch($booking->status)
                                            @case('pending')
                                                <span class="badge bg-warning">Chờ xác nhận</span>
                                                @break
                                            @case('confirmed')
                                                <span class="badge bg-info">Đã xác nhận</span>
                                                @break
                                            @case('completed')
                                                <span class="badge bg-success">Hoàn thành</span>
                                                @break
                                            @case('cancelled')
                                                <span class="badge bg-danger">Đã hủy</span>
                                                @break
                                        @endswitch
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- Thông tin thanh toán -->
                        <div class="col-md-6">
                            <h5 class="mb-3">Thanh toán</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td class="ps-0"><strong>Phương thức:</strong></td>
                                     <td>{{ $booking->payment_method == 'cash' ? 'Tiền mặt' : ($booking->payment_method == 'balance' ? "Thanh toán bằng số dư" : 'Chuyển khoản' )}}</td>
                                </tr>
                                <tr>
                                    <td class="ps-0"><strong>Tổng tiền:</strong></td>
                                    <td>{{ number_format($booking->total_amount) }}đ</td>
                                </tr>
                                <tr>
                                    <td class="ps-0"><strong>Trạng thái:</strong></td>
                                    <td>
                                        @if($booking->payment_status == 'paid')
                                            <span class="badge bg-success">Đã thanh toán</span>
                                        @else
                                            <span class="badge bg-warning">Chưa thanh toán</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Thông tin chuyển khoản nếu có -->
                    @if($booking->payment_method == 'bank_transfer' && $booking->payment_status != 'paid')
                        @php
                            $bankAccount = \App\Models\AdminBankAccount::where('main',1)->first();
                        @endphp
                        <div class="alert alert-info mb-4">
                            <h5 class="alert-heading mb-3">Thông tin chuyển khoản</h5>
                            <table class="table table-borderless mb-0">
                                <tr>    
                                    <td style="width: 150px"><strong>Ngân hàng:</strong></td>
                                    <td>{{ $bankAccount->bank_name ?? '' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Số tài khoản:</strong></td>
                                    <td>{{ $bankAccount->account_number ?? '' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Chủ tài khoản:</strong></td>
                                    <td>{{ $bankAccount->account_holder ?? '' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Nội dung:</strong></td>
                                    <td>STUDIO {{ $booking->id }}</td>
                                </tr>
                            </table>
                        </div>
                    @endif

                    <!-- Ghi chú nếu có -->
                    @if($booking->notes)
                        <div class="mb-4">
                            <h5>Ghi chú</h5>
                            <p class="mb-0">{{ $booking->notes }}</p>
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="mt-4 d-flex justify-content-between">
                        <a href="javascript:void(0);" onclick="window.history.back();" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại
                        </a>

                        @if($booking->status == 'pending')
                            <form action="{{ route('bookings.cancel', $booking) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-danger"
                                        onclick="return confirm('Bạn có chắc muốn hủy đặt phòng này?')">
                                    <i class="fas fa-times me-2"></i>Hủy đặt phòng
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
