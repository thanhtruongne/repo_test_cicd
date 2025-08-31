<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Recording Studio</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('img/logo.png') }}?ver={{ time() }}" type="image/png">
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?ver={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}?ver={{ time() }}">
    @yield('styles')
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="https://hoi.com.vn/">
                <img src="{{ asset('img/logo.png') }}" alt="">

            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('rooms.index') }}">Đặt lịch Phòng thu</a>
                    </li>


                </ul>

                <ul class="navbar-nav align-items-center">
                    @auth
                        <li class="nav-item">
                            <h3 class="nav-link font-medium text-sm leading-6 mb-0 " id="navbarDropdown" role="button" >
                               <span id="total_balance" data-balance="{{ session('balance_' . auth()->id()) }}"> {{ number_format(session()->get('balance_' . auth()->id()) ?? 0) }}</span>
                               <i class="fa-solid fa-coins" style="color: #d1992c;"></i>
                            </h3>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                data-bs-toggle="dropdown">
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('bookings.index') }}">Lịch đặt của tôi</a></li>
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Thông tin cá nhân</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Đăng xuất</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="http://127.0.0.1:8000/cross-platform/authentication?redirect={{ route('callback') }}">Đăng nhập</a>
                        </li>
                        {{-- <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Đăng ký</a>
                        </li> --}}
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content -->
    {{-- @if (session('success'))
        <div class="container">
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        </div>
    @endif --}}
    <main class="py-4 pt-0">


        @if (session('error'))
            <div class="container">
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Về chúng tôi</h5>
                    <p>Studio chuyên nghiệp với trang thiết bị hiện đại</p>
                </div>
                <div class="col-md-4">
                    <h5>Liên hệ</h5>
                    <p>
                        <i class="fas fa-phone"></i> 0822281194<br>
                        <i class="fas fa-envelope"></i> info@studio.com<br>
                        <i class="fas fa-location-dot"></i> 31/40C Ung Văn Khiêm, p25, bình thạnh

                    </p>
                </div>
                <div class="col-md-4">
                    <h5>Theo dõi chúng tôi</h5>
                    <div class="social-links">
                        <a href="#" class="text-light me-2"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="#" class="text-light me-2"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" class="text-light me-2"><i class="fab fa-youtube fa-lg"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
         function numberFormat(number, decimals = 0, decimalPoint = '.', thousandsSeparator = ',') {
            number = parseFloat(number).toFixed(decimals);
            let [integerPart, decimalPart] = number.split('.');
            integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, thousandsSeparator);
            return decimals > 0 ? `${integerPart}${decimalPoint}${decimalPart}` : integerPart;
        }

        let email = '{{ auth()?->user()?->email }}';
        let retries = 0;
        let balanceCheckInterval;

        function check_balance() {
            if(!email) {
                clearInterval(balanceCheckInterval)
                return;
            }
            if(retries >= 3) {
                clearInterval(balanceCheckInterval)
                alert('Có lỗi xảy ra . Vui lòng nhấn refresh trang.')
                return;
            }
            $.ajax({
                url:'{{ route('get-profile-3rd-info') }}',
                method: 'POST',
                data : {
                    email
                },
                dataType: "json",
                success: function (response) {
                    if(response?.status ) {
                        let balanceSpan = $('#total_balance');
                        let previous_balance = $(balanceSpan).data('balance');
                        let current_balance =  +response?.data?.balance ?? 0;

                        if(current_balance !== previous_balance) {
                           $(balanceSpan).text(numberFormat(current_balance));
                           $(balanceSpan).attr('data-balance',current_balance);
                        }
                        clearInterval(balanceCheckInterval);

                    }
                    retries++
                },
                error: function (xhr, status, error) {
                    alert(error.message)
                    if (xhr.status === 500) {
                        clearInterval(balanceCheckInterval);
                        console.log('Dừng kiểm tra do lỗi nghiêm trọng.');
                    }
                }
            });
        }
        balanceCheckInterval = setInterval(check_balance, 1000);
    </script>
    @yield('scripts')
</body>

</html>
