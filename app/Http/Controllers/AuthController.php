<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validasi request termasuk validasi role
        $validatedData = Validator::make($request->all(), [
            'username'     => 'required|without_spaces|string|max:255 ',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($validatedData->fails()) {
            return response()->json([
                'status_code' => 400,
                'message' => $validatedData->errors()->first(),
                'data'    => null,
            ], 400);
        }

        try {
            // Buat user baru
            $user = new User;
            $user->name     = $request->name;
            $user->email    = $request->email;
            $user->password = Hash::make($request->password);
            $user->role_id     = $request->role_id;
            $user->save();

            // Return response sukses
            return response()->json([
                'status_code' => 201,
                'message' => 'User created successfully',
                'data'    => $user,

            ], 201);
        } catch (Exception $e) {
            // Return response gagal
            return response()->json([
                'status_code' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $credentials = $request->only('email', 'password');

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json([
                'message' => 'Email atau password salah',
                'status_code' => 401,
                'data' => null
            ], 401);
        }

        $user = Auth::guard('api')->user();

        $formatedUser = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role->name,
            'token' => $token,
        ];
        return response()->json([
            'message' => 'Login berhasil',
            'status_code' => 200,
            'data' => $formatedUser,

        ], 200);
    }

    public function me()
    {
        try {
            $user = Auth::guard('api')->user();

            $formatedUser = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->name,
            ];

            return response()->json([
                'message' => 'User ditemukan',
                'status_code' => 200,
                'data' => $formatedUser
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status_code' => 500,
                'data' => null
            ], 500);
        }
    }

    public function logout()
    {
        Auth::guard('api')->logout();

        return response()->json([
            'message' => 'Logout berhasil',
            'status_code' => 200,
            'data' => null
        ]);
    }
}
