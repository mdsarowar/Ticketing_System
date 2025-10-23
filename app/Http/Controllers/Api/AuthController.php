<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function showLogin()
    {
//        return 'sarowar';
        return Inertia::render('Auth/login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        return Inertia::render('Auth/register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,customer',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        Auth::login($user);

        return redirect('/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
    // Register
//    public function register(Request $request)
//    {
//        $request->validate([
//            'name' => 'required|string|max:255',
//            'email' => 'required|string|email|max:255|unique:users',
//            'password' => 'required|string|min:8|confirmed',
//            'role' => 'required|in:admin,customer',
//        ]);
//
//        $user = User::create([
//            'name' => $request->name,
//            'email' => $request->email,
//            'password' => Hash::make($request->password),
//            'role' => $request->role,
//        ]);
//
//        $token = $user->createToken('auth_token')->plainTextToken;
//
//        return response()->json([
//            'success' => true,
//            'message' => 'User registered successfully',
//            'data' => [
//                'user' => $user,
//                'token' => $token,
//            ],
//        ], 201);
//    }
//
//    // Login
//    public function login(Request $request)
//    {
//        $request->validate([
//            'email' => 'required|email',
//            'password' => 'required',
//        ]);
//
//        $user = User::where('email', $request->email)->first();
//
//        if (!$user || !Hash::check($request->password, $user->password)) {
//            throw ValidationException::withMessages([
//                'email' => ['The provided credentials are incorrect.'],
//            ]);
//        }
//
//        // Delete old tokens
//        $user->tokens()->delete();
//
//        $token = $user->createToken('auth_token')->plainTextToken;
//
//        return response()->json([
//            'success' => true,
//            'message' => 'Login successful',
//            'data' => [
//                'user' => $user,
//                'token' => $token,
//            ],
//        ]);
//    }
//
//    // Logout
//    public function logout(Request $request)
//    {
//        $request->user()->currentAccessToken()->delete();
//
//        return response()->json([
//            'success' => true,
//            'message' => 'Logged out successfully',
//        ]);
//    }
//
//    // Get authenticated user
//    public function user(Request $request)
//    {
//        return response()->json([
//            'success' => true,
//            'data' => $request->user(),
//        ]);
//    }
}
