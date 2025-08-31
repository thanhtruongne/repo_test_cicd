@extends('layouts.app')

@section('title', 'Trang chủ')

@section('content')



<!-- Featured Rooms -->
<div class="container py-5">
    <h2 class="text-center mb-4">Phòng Thu Nổi Bật</h2>
    <div class="row g-4">
        @foreach($featuredRooms as $room)
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <img src="{{ $room->image_url }}" class="card-img-top" alt="{{ $room->name }}" 
                     style="height: 200px; object-fit: cover;">
                <div class="card-body">
                    <h5 class="card-title">{{ $room->name }}</h5>
                    <p class="card-text">{{ Str::limit($room->description, 100) }}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-primary fw-bold">
                            {{ number_format($room->hourly_rate) }}đ/giờ
                        </span>
                        <a href="{{ route('rooms.show', $room) }}" class="btn btn-outline-primary">
                            Chi tiết
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

   
</div>




@endsection