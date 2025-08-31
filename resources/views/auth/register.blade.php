@extends('layouts.app')

@section('title', 'Đăng ký')

@section('content')
<div class="container py-5">
   <div class="row justify-content-center">
       <div class="col-md-5">
           <div class="card border-0 shadow-sm">
               <div class="card-header bg-transparent px-4 py-3 border-0">
                   <h4 class="text-center mb-0">Đăng ký</h4>
               </div>

               <div class="card-body p-4">
                   @if($errors->any())
                       <div class="alert alert-danger">
                           <ul class="mb-0">
                               @foreach($errors->all() as $error)
                                   <li>{{ $error }}</li>
                               @endforeach
                           </ul>
                       </div>
                   @endif

                   <form method="POST" action="{{ route('register') }}">
                       @csrf

                       <div class="mb-3">
                           <label for="name" class="form-label">Họ tên</label>
                           <div class="input-group">
                               <span class="input-group-text bg-light border-end-0">
                                   <i class="fas fa-user text-muted"></i>
                               </span>
                               <input type="text" class="form-control border-start-0 @error('name') is-invalid @enderror"
                                      id="name" name="name" value="{{ old('name') }}"
                                      placeholder="Nhập họ tên" required autofocus>
                               @error('name')
                                   <div class="invalid-feedback">{{ $message }}</div>
                               @enderror
                           </div>
                       </div>

                       <div class="mb-3">
                           <label for="email" class="form-label">Email</label>
                           <div class="input-group">
                               <span class="input-group-text bg-light border-end-0">
                                   <i class="fas fa-envelope text-muted"></i>
                               </span>
                               <input type="email" class="form-control border-start-0 @error('email') is-invalid @enderror"
                                      id="email" name="email" value="{{ old('email') }}"
                                      placeholder="name@example.com" required>
                               @error('email')
                                   <div class="invalid-feedback">{{ $message }}</div>
                               @enderror
                           </div>
                       </div>

                       <div class="mb-3">
                           <label for="phone" class="form-label">Số điện thoại</label>
                           <div class="input-group">
                               <span class="input-group-text bg-light border-end-0">
                                   <i class="fas fa-phone text-muted"></i>
                               </span>
                               <input type="text" class="form-control border-start-0 @error('phone') is-invalid @enderror"
                                      id="phone" name="phone" value="{{ old('phone') }}"
                                      placeholder="Nhập số điện thoại" required>
                               @error('phone')
                                   <div class="invalid-feedback">{{ $message }}</div>
                               @enderror
                           </div>
                       </div>

                       <div class="mb-3">
                           <label for="password" class="form-label">Mật khẩu</label>
                           <div class="input-group">
                               <span class="input-group-text bg-light border-end-0">
                                   <i class="fas fa-lock text-muted"></i>
                               </span>
                               <input type="password" class="form-control border-start-0 @error('password') is-invalid @enderror"
                                      id="password" name="password" required
                                      placeholder="Nhập mật khẩu">
                               @error('password')
                                   <div class="invalid-feedback">{{ $message }}</div>
                               @enderror
                           </div>
                       </div>

                       <div class="mb-4">
                           <label for="password_confirmation" class="form-label">Xác nhận mật khẩu</label>
                           <div class="input-group">
                               <span class="input-group-text bg-light border-end-0">
                                   <i class="fas fa-lock text-muted"></i>
                               </span>
                               <input type="password" class="form-control border-start-0"
                                      id="password_confirmation" name="password_confirmation" required
                                      placeholder="Nhập lại mật khẩu">
                           </div>
                       </div>

                       <button type="submit" class="btn btn-primary w-100 mb-3">
                           <i class="fas fa-user-plus me-2"></i>Đăng ký
                       </button>
                   </form>
               </div>

               <div class="card-footer bg-transparent text-center border-0 pb-4">
                   <p class="mb-0">Đã có tài khoản? 
                       <a href="{{ route('login') }}" class="text-primary">Đăng nhập</a>
                   </p>
               </div>
           </div>
       </div>
   </div>
</div>
@endsection