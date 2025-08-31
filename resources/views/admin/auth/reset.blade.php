@extends('layouts.admin')
@section('change-password-active', 'active')

@section('page-title', 'Change Password')

@section('styles')
<style>
    .reset-container {
        min-height: 70vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .reset-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(102,126,234,0.08);
        padding: 32px 28px;
        max-width: 400px;
        width: 100%;
        margin: 0 auto;
        border: 1px solid #f0f0f0;
    }
    .reset-card h3 {
        font-weight: 700;
        color: #667eea;
        margin-bottom: 18px;
        font-size: 24px;
    }
    .reset-card .form-label {
        font-weight: 500;
        color: #2c3e50;
        margin-bottom: 6px;
    }
    .reset-card .form-control {
        border-radius: 10px;
        border: 1.5px solid #e1e8ed;
        padding: 12px 14px;
        font-size: 15px;
        margin-bottom: 12px;
        transition: border-color 0.2s;
    }
    .reset-card .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 2px rgba(102,126,234,0.08);
    }
    .reset-card .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 10px;
        font-weight: 600;
        font-size: 16px;
        padding: 12px;
        margin-top: 8px;
        width: 100%;
        transition: box-shadow 0.2s;
    }
    .reset-card .btn-primary:hover {
        box-shadow: 0 4px 16px rgba(102,126,234,0.15);
    }
    .reset-card .alert {
        font-size: 14px;
        border-radius: 8px;
        margin-bottom: 14px;
        padding: 10px 14px;
    }
    .reset-card .mt-3 a {
        color: #667eea;
        text-decoration: underline;
        font-size: 14px;
    }
</style>
@endsection

@section('content')
    <div class="reset-container">
        <div class="reset-card">
            <h3 class="mb-3 text-center">Reset admin password</h3>
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif
            <form method="POST" action="{{ route('admin.reset.submit') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" required autofocus placeholder="Nhập email admin">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">New password</label>
                    <input type="password" name="password" id="password" class="form-control" required placeholder="Nhập mật khẩu mới">
                </div>
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirm new password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required placeholder="Nhập lại mật khẩu mới">
                </div>
                <button type="submit" class="btn btn-primary w-100">Change password</button>
            </form>
            <!--<div class="mt-3 text-center">-->
            <!--    <a href="{{ route('admin.login') }}">Back to login</a>-->
            <!--</div>-->
        </div>
    </div>
@endsection