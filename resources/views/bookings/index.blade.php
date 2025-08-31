@extends('layouts.app')

@section('title', 'Lịch sử đặt phòng')

@section('content')
<div class="container py-4">
   <h4 class="mb-4">Lịch sử đặt phòng</h4>

   <div class="card border-0 shadow-sm">
       <div class="card-body p-0">
           <div class="table-responsive">
               <table class="table table-hover mb-0">
                   <thead class="bg-light">
                       <tr>
                           <th>Mã đặt phòng</th>
                           <th>Phòng</th>
                           <th>Thời gian</th>
                           <th>Tổng tiền</th>
                           <th>Trạng thái</th>
                           <th>Thanh toán</th>
                           <th width="100"></th>
                       </tr>
                   </thead>
                   <tbody>
                       @forelse($bookings as $booking)
                       <tr>
                           <td>#{{ $booking->id }}</td>
                           <td>{{ $booking->room->name }}</td>
                           <td>
                               {{ $booking->start_time->format('H:i d/m/Y') }} 
                               <br>
                               <small class="text-muted">{{ $booking->total_hours }} giờ</small>
                           </td>
                           <td>{{ number_format($booking->total_amount) }}đ</td>
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
                           <td>
                               @if($booking->payment_status == 'paid')
                                   <span class="badge bg-success">Đã thanh toán</span>
                               @else
                                   <span class="badge bg-warning">Chưa thanh toán</span>
                               @endif
                           </td>
                           <td>
                               <a href="{{ route('bookings.show', $booking) }}" class="btn btn-sm btn-light">
                                   Chi tiết
                               </a>
                           </td>
                       </tr>
                       @empty
                       <tr>
                           <td colspan="7" class="text-center py-4">Chưa có lịch đặt phòng nào</td>
                       </tr>
                       @endforelse
                   </tbody>
               </table>
           </div>

           @if($bookings->hasPages())
           <div class="p-3 border-top">
               {{ $bookings->links() }}
           </div>
           @endif
       </div>
   </div>
</div>
@endsection