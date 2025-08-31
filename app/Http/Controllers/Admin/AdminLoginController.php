<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    /**
     * Hiển thị form đăng nhập cho admin.
     */
    public function showLoginForm()
    {
        if(auth()->check() && auth()->user()->isAdmin()){
            return redirect(route("admin.dashboard"));
        }
        // return view('admin.auth.login');
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Nếu thông tin hợp lệ
        if (Auth::attempt($credentials)) {
            // Kiểm tra quyền là admin
            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } else {
                // Không phải admin thì logout và thông báo
                Auth::logout();
                return redirect()->route('admin.login')->withErrors([
                    'email' => 'Bạn không có quyền truy cập khu vực admin.',
                ]);
            }
        }

        // Sai thông tin đăng nhập
        return redirect()->back()->withErrors([
            'email' => 'Tài khoản hoặc mật khẩu không đúng.',
        ]);
    }

    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('admin.login');
    }

    public function showResetForm()
    {
        return view('admin.auth.reset');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed'
        ]);

        $user = \App\Models\User::where('email', $request->email)->where('role', 'admin')->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Không tìm thấy tài khoản admin với email này.']);
        }

        $user->password = bcrypt($request->password);
        $user->save();

        return redirect()->route('admin.dashboard')->with('status', 'Password changed successfully. You can log in with the new password.');
    }
}
               
