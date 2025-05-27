<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Buyer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function AdminProfile(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validatedData->fails()) {
            return response()->json([
                'status_code' => 400,
                'message' => $validatedData->errors()->first(),
                'data' => null,
            ], 400);
        }


        try {
            $user = Auth::guard('api')->user();
            if (Admin::where('user_id', $user->id)->exists()) {
                return response()->json([
                    'message' => 'Profil admin sudah ada',
                    'status_code' => 409,
                    'data' => null
                ], 409);
            }
            $admin = new Admin();
            $admin->user_id = $user->id;
            $admin->name = $request->name;
            $admin->save();

            return response()->json([
                'message' => 'Profil admin berhasil dibuat',
                'status_code' => 201,
                'data' => [
                    'id' => $admin->id,
                    'name' => $admin->name,
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Internal Server Error: ' . $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    public function addBuyerProfile(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        if ($validatedData->fails()) {
            return response()->json([
                'status_code' => 400,
                'message' => $validatedData->errors()->first(),
                'data' => null,
            ], 400);
        }

        try {
            $user = Auth::guard('api')->user();
            if (Buyer::where('user_id', $user->id)->exists()) {
                return response()->json([
                    'message' => 'Profil pembeli sudah ada',
                    'status_code' => 409,
                    'data' => null
                ], 409);
            }

            $buyer = new Buyer();
            $buyer->user_id = $user->id;
            $buyer->name = $request->name;
            $buyer->address = $request->address;
            $buyer->phone = $request->phone;

            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $path = Storage::putFile('public/photos', $file);
                $buyer->photo = Storage::url($path);
            }

            $buyer->save();

            return response()->json([
                'message' => 'Profil pembeli berhasil dibuat',
                'status_code' => 201,
                'data' => [
                    'id' => $buyer->id,
                    'name' => $buyer->name,
                    'address' => $buyer->address,
                    'phone' => $buyer->phone,
                    'photo' => $buyer->photo,
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Internal Server Error: ' . $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }
}
