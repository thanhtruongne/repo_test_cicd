<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;

class ProfileController extends Controller
{
   public function edit()
   {
       return view('profile.edit', [
           'user' => auth()->user()
       ]);
   }

   public function update(Request $request)
   {
       $validated = $request->validate([
           'name' => 'required|string|max:255',
           'phone' => [
            'required',
            'string',
            'max:20',
            Rule::unique('users')->ignore(auth()->id())->where(function ($query) {
                return $query->where('phone', '!=', auth()->user()->phone);
            })
        ]
       ]);

       auth()->user()->update($validated);

       return back()->with('success', 'Cập nhật thông tin thành công');
   }

   public function updatePassword(Request $request) 
   {
       $validated = $request->validate([
           'current_password' => ['required', function($attribute, $value, $fail) {
               if (!Hash::check($value, auth()->user()->password)) {
                   $fail('Mật khẩu hiện tại không đúng');
               }
           }],
           'password' => 'required|string|min:8|confirmed'
       ]);

       auth()->user()->update([
           'password' => Hash::make($validated['password'])
       ]);

       return back()->with('success', 'Đổi mật khẩu thành công');
   }
    public function getUser3rdInfo(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);
        $client = new Client();
        try {
            $apiResponse = $client->request('POST', env('ORDER_API_URL').'3rd/user/get-data-user', [
                'headers' => [
                    'x-api-key' => 'ebc57e0b-8308-41e2-8932-9ad69c405f20'
                ],
                'form_params' => [
                    'email' => $validated['email']
                ]
            ]);
            $profileData = json_decode($apiResponse->getBody(), true);
            $userData = $profileData['data'] ?? null;
            if (!$userData) {
                return response()->json(['message' => 'Không thể lấy thông tin người dùng', 'status' => false], 419);
            }
            if (session()->get('balance_' . auth()->id()) != +$userData['balance']) {
                session()->put('balance_' . auth()->id(), +$userData['balance']);
            }

            return response()->json(['message' => 'Lấy thông tin user thành công', 'status' => true, 'data' => $userData], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], $e->getCode());
        }
    }
}