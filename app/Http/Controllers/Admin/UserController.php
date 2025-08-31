<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::query();
        $query->where('id','!=', auth()->id()); 

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(10);
        
        return view('admin.user.index', compact('users'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request) 
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
            'role' => 'required|in:user,admin',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json([
            'success' => true, 
            'message' => 'User created successfully',
            'user' => $user
        ]);
    }

    /**
     * Get user data for editing
     */
    public function edit(User $user) 
    {
        return response()->json($user);
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user) 
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:user,admin',
        ]);

        // Only hash password if provided
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'success' => true, 
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user) 
    {
        try {
            // Check if user has bookings
            if ($user->bookings()->count() > 0) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Cannot delete user with existing bookings'
                ]);
            }

            // Don't allow deleting the last admin
            if ($user->isAdmin() && User::where('role', 'admin')->count() <= 1) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Cannot delete the last admin user'
                ]);
            }

            $user->delete();

            return response()->json([
                'success' => true, 
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Error deleting user: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get user statistics for dashboard
     */
    public function getStats()
    {
        return [
            'total_users' => User::count(),
            'active_users' => User::whereNotNull('email_verified_at')->count(),
            'new_users_this_month' => User::whereMonth('created_at', now()->month)->count(),
            'admin_users' => User::where('role', 'admin')->count(),
        ];
    }
}