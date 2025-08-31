@extends('layouts.app')

@section('title', 'Trang chủ')

@section('content')
<!-- Hero Section -->
<div class="hero bg-dark text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 order-2 order-md-1 mt-4 mt-md-0 text-center text-md-start">
                <h1 class="display-4">Studio Chuyên Nghiệp</h1>
                <p class="lead">Đặt phòng thu âm trực tuyến dễ dàng, nhanh chóng</p>
                <a href="{{ route('rooms.index') }}" class="btn btn-primary">
                    Đặt phòng ngay
                </a>
            </div>
            <div class="col-md-6 order-1 order-md-2">
                <img src="https://images.unsplash.com/photo-1598488035139-bdbb2231ce04" alt="Studio" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</div>

<!-- Stats Section -->
<div class="py-5 bg-light">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-microphone-alt fa-3x text-primary mb-3"></i>
                        <h3>{{ $featuredRooms->count() }}</h3>
                        <h5>Phòng Thu</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-calendar-check fa-3x text-success mb-3"></i>
                        <h3>{{ $totalBookings }}</h3>
                        <h5>Lượt Đặt Phòng</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-users fa-3x text-info mb-3"></i>
                        <h3>{{ $totalCustomers }}</h3>
                        <h5>Khách Hàng</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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

    <div class="text-center mt-4">
        <a href="{{ route('rooms.index') }}" class="btn btn-primary">
            Xem tất cả phòng thu
        </a>
    </div>
</div>

<!-- Features Section -->
<div class="bg-light py-5">
    <div class="container">
        <h2 class="text-center mb-4">Tại Sao Chọn Chúng Tôi?</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-medal fa-3x text-primary mb-3"></i>
                        <h4>Thiết Bị Hiện Đại</h4>
                        <p>Trang bị đầy đủ thiết bị thu âm chuyên nghiệp từ các thương hiệu hàng đầu</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-user-tie fa-3x text-primary mb-3"></i>
                        <h4>Kỹ Thuật Viên</h4>
                        <p>Đội ngũ kỹ thuật viên nhiều năm kinh nghiệm, tận tâm hỗ trợ</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-clock fa-3x text-primary mb-3"></i>
                        <h4>Linh Hoạt</h4>
                        <p>Đặt phòng linh hoạt theo giờ, hỗ trợ 24/7</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contact Section -->
<div class="container py-5">
    <div class="row align-items-center">
        <div class="col-md-6 mb-4 mb-md-0">
            <h2>Liên Hệ Với Chúng Tôi</h2>
            <p class="lead">Để được tư vấn và hỗ trợ chi tiết, vui lòng liên hệ:</p>
            <ul class="list-unstyled">
                <li class="mb-3">
                    <i class="fas fa-phone text-primary me-2"></i> 0123.456.789
                </li>
                <li class="mb-3">
                    <i class="fas fa-envelope text-primary me-2"></i> info@studio.com
                </li>
                <li class="mb-3">
                    <i class="fas fa-map-marker-alt text-primary me-2"></i> 
                    123 Đường ABC, Quận XYZ, TP.HCM
                </li>
            </ul>
            <div class="mt-4">
                <a href="#" class="text-primary me-3"><i class="fab fa-facebook fa-2x"></i></a>
                <a href="#" class="text-primary me-3"><i class="fab fa-instagram fa-2x"></i></a>
                <a href="#" class="text-primary"><i class="fab fa-youtube fa-2x"></i></a>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow">
                <div class="card-body">
                    <h4 class="card-title mb-4">Gửi Yêu Cầu Tư Vấn</h4>
                    
                    <!-- Alert Messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('notifications.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   placeholder="Họ và tên *" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   placeholder="Email *" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                                   placeholder="Số điện thoại" value="{{ old('phone') }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror" 
                                   placeholder="Tiêu đề" value="{{ old('subject') }}">
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <textarea name="message" class="form-control @error('message') is-invalid @enderror" 
                                      rows="4" placeholder="Nội dung *" required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-paper-plane me-2"></i>Gửi yêu cầu
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@if(session('success'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.querySelector('form[action="{{ route("notifications.store") }}"]');
    if (contactForm) {
        contactForm.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});
</script>
@endif