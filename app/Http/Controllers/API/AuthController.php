<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Role;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\Controller;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:student,teacher,parent',
            'first_name' => 'required',
            'last_name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Get role ID
        $role = Role::where('name', $request->role)->first();

        // Create user
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $role->id,
        ]);

        // Create user profile
        UserProfile::create([
            'user_id' => $user->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'date_of_birth' => $request->date_of_birth

        ]);

        // Create specific role-based record
        if ($request->role === 'teacher') {
            $user->teacher()->create([
                'teacher_id' => 'TEA' . str_pad($user->id, 3, '0', STR_PAD_LEFT),
                'joining_date' => now(),
            ]);
        } elseif ($request->role === 'student') {
            $student = $user->student()->create([
                'student_id' => 'STU' . str_pad($user->id, 3, '0', STR_PAD_LEFT),
                'admission_date' => now(),
            ]);
        }

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user->load(['role', 'profile', 'student', 'teacher']),
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only(['username', 'password']);

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $user = JWTAuth::user();

        // Check if user is active
        if (!$user->is_active) {
            JWTAuth::logout();
            return response()->json(['error' => 'Account is deactivated'], 403);
        }

        // Update last login
        $user->update(['last_login' => now()]);

        return response()->json([
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role->name,
                'permissions' => $user->getAllPermissions(),
                'profile' => $user->profile,
            ],
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ]);
    }

    public function logout()
    {
        JWTAuth::logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return response()->json([
            'token' => JWTAuth::refresh(),
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ]);
    }
}
