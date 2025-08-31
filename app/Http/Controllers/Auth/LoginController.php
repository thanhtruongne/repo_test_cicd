<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;


class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không chính xác.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        session()->forget('balance_' . auth()->id());
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
    public function callback(Request $request)
    {
        $response = $request->all();
        $token = $response['token'] ?? null;

        if (!$token) {
            return redirect()->route('home')->with('error', 'Đăng nhập thất bại');
        }
        $client = new Client();
        try {
            $apiResponse = $client->request('GET', env('ORDER_API_URL') . 'profile', [
                'headers' => [
                    'x-api-key' => env('ORDER_API_KEY'),
                    'Authorization' => 'Bearer ' . $token,
                ],
                'form_params' => []
            ]);

            $profileData = json_decode($apiResponse->getBody(), true);

            // Get user data from response
            $userData = $profileData['data'] ?? null;
            if (!$userData) {
                return redirect()->route('home')->with('error', 'Không thể lấy thông tin người dùng');
            }
            // Check if user exists
            $user = User::where('email', $userData['email'])->first();
            if (!$user) {
                // Create new user
                $user = User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'] ?? $userData['phone'] . '@hoi.com.vn',
                    'phone' => $userData['phone'],
                    'password' => Hash::make(Str::random(12)),
                    'role' => 'user'
                ]);
            }
            //set case tạm
            session()->put('balance_' . $user->id, +$userData['balance']);
            // Login user

            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->intended('/');
        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'Không thể lấy thông tin người dùng: ' . $e->getMessage());
        }
    }
}
