<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddAdminRequest;
use App\Http\Requests\AddBuyerRequest;
use App\Models\Admin;
use App\Models\Buyer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{

    /**
     * Tambah profil admin
     *
     * Endpoint untuk membuat profil admin. Hanya bisa dilakukan satu kali per user.
     *
     * @authenticated
     * @bodyParam name string required Nama admin. Contoh: Andi
     *
     * @response 201 {
     *   "message": "Profil admin berhasil dibuat",
     *   "status_code": 201,
     *   "data": {
     *     "id": 1,
     *     "name": "Andi"
     *   }
     * }
     * @response 400 {
     *   "status_code": 400,
     *   "message": "The name field is required.",
     *   "data": null
     * }
     * @response 409 {
     *   "message": "Profil admin sudah ada",
     *   "status_code": 409,
     *   "data": null
     * }
     * @response 500 {
     *   "status_code": 500,
     *   "message": "Internal Server Error: ...",
     *   "data": null
     * }
     */
    public function AdminProfile(AddAdminRequest $request)
    {

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

    public function getAdminProfile()
    {
        try {
            $user = Auth::guard('api')->user();
            $admin = Admin::where('user_id', $user->id)->first();

            if (!$admin) {
                return response()->json([
                    'message' => 'Profil admin tidak ditemukan',
                    'status_code' => 404,
                    'data' => null
                ], 404);
            }

            return response()->json([
                'message' => 'Profil admin ditemukan',
                'status_code' => 200,
                'data' => [
                    'id' => $admin->id,
                    'name' => $admin->name,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Internal Server Error: ' . $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    /**
     * Tambah profil pembeli
     *
     * Endpoint untuk membuat profil pembeli. Hanya bisa dilakukan satu kali per user.
     *
     * @authenticated
     * @bodyParam name string required Nama pembeli. Contoh: Budi
     * @bodyParam address string required Alamat pembeli. Contoh: Jl. Merdeka No. 10
     * @bodyParam phone string required Nomor telepon pembeli. Contoh: 08123456789
     * @bodyParam photo file Foto pengguna (JPEG/PNG/JPG/GIF). Tidak wajib.
     *
     * @response 201 {
     *   "message": "Profil pembeli berhasil dibuat",
     *   "status_code": 201,
     *   "data": {
     *     "id": 2,
     *     "name": "Budi",
     *     "address": "Jl. Merdeka No. 10",
     *     "phone": "08123456789",
     *     "photo": "/storage/photos/abc123.jpg"
     *   }
     * }
     * @response 400 {
     *   "status_code": 400,
     *   "message": "The phone field is required.",
     *   "data": null
     * }
     * @response 409 {
     *   "message": "Profil pembeli sudah ada",
     *   "status_code": 409,
     *   "data": null
     * }
     * @response 500 {
     *   "status_code": 500,
     *   "message": "Internal Server Error: ...",
     *   "data": null
     * }
     */

    public function addBuyerProfile(AddBuyerRequest $request)
    {

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
                $path = $request->file('photo')->store('photos', 'public');
                $buyer->photo = $path; // simpan hanya photos/abc.jpg

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

    public function getBuyerProfile()
    {
        try {
            $user = Auth::guard('api')->user();
            $buyer = Buyer::where('user_id', $user->id)->first();

            if (!$buyer) {
                return response()->json([
                    'message' => 'Profil pembeli tidak ditemukan',
                    'status_code' => 404,
                    'data' => null
                ], 404);
            }

            $image = $buyer->photo ? asset('storage/' . $buyer->photo) : null;

            return response()->json([
                'message' => 'Profil pembeli ditemukan',
                'status_code' => 200,
                'data' => [
                    'id' => $buyer->id,
                    'name' => $buyer->name,
                    'address' => $buyer->address,
                    'phone' => $buyer->phone,
                    'photo' => $image,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Internal Server Error: ' . $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }
}
