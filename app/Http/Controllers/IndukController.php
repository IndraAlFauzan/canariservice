<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIndukRequest;
use App\Models\Induk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Exception;

class IndukController extends Controller
{
    /**
     * Ambil semua data induk
     *
     * @authenticated
     * @response 200 {
     *   "message": "Data induk berhasil diambil",
     *   "status_code": 200,
     *   "data": [
     *     {
     *       "id": 1,
     *       "no_ring": "RING-001",
     *       "tanggal_lahir": "2024-01-15",
     *       "jenis_kelamin": "jantan",
     *       "jenis_kenari": "Yorkshire",
     *       "keterangan": "Induk aktif",
     *       "gambar_induk": "/storage/photos/induk1.jpg",
     *       "created_at": "2024-01-15T12:00:00Z",
     *      "updated_at": "2024-01-15T12:00:00Z"
     *          },
     *     {
     *      "id": 1,
     *      "no_ring": "RING-001",
     *      "tanggal_lahir": "2024-01-15",
     *      "jenis_kelamin": "jantan",
     *      "jenis_kenari": "Yorkshire",
     *      "keterangan": "Induk aktif",
     *      "gambar_induk": "/storage/photos/induk1.jpg",
     *      "created_at": "2024-01-15T12:00:00Z",
     *      "updated_at": "2024-01-15T12:00:00Z"
     *  },
     * ]
     * }
     * 
     * @response 204 {
     *  "message": "Tidak ada data induk",
     * "status_code": 204,
     * "data": []
     * }
     * @response 500 {
     *  "status_code": 500,
     * "message": "Internal Server Error: ...",
     * "data": null
     * }
     */
    public function index()
    {
        $data = Induk::all();

        try {
            if ($data->isEmpty()) {
                return response()->json([
                    'message' => 'Tidak ada data induk',
                    'status_code' => 204,
                    'data' => [],
                ]);
            }
            return response()->json([
                'message' => 'Data induk berhasil diambil',
                'status_code' => 200,
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Internal Server Error: ' . $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    /**
     * Tambah data induk
     *
     * @authenticated
     * @bodyParam no_ring string No cincin. Contoh: RING-001
     * @bodyParam tanggal_lahir date required Tanggal lahir. Contoh: 2024-01-15
     * @bodyParam jenis_kelamin string required Jenis kelamin. Contoh: jantan
     * @bodyParam jenis_kenari string required Jenis kenari. Contoh: Yorkshire
     * @bodyParam keterangan string Keterangan tambahan. Contoh: Induk aktif
     * @bodyParam gambar_induk file Gambar burung (JPEG/PNG/JPG)
     *
     * @response 201 {
     *   "message": "Data induk berhasil ditambahkan",
     *   "status_code": 201,
     *   "data": 
     * {
     *     "id": 1,
     *    "no_ring": "RING-001",
     *   "tanggal_lahir": "2024-01-15",
     *   "jenis_kelamin": "jantan",
     *   "jenis_kenari": "Yorkshire",
     *   "keterangan": "Induk aktif",
     *  "gambar_induk": "/storage/photos/induk1.jpg",
     *   "created_at": "2024-01-15T12:00:00Z",
     *  "updated_at": "2024-01-15T12:00:00Z"
     *  },
     *    
     *          
     * }
     */
    public function store(StoreIndukRequest $request)
    {
        try {
            $user = Auth::guard('api')->user();


            $induk = new Induk();

            // Validasi apakah no_ring sudah ada
            if (Induk::where('no_ring', $request->no_ring)->exists()) {
                return response()->json([
                    'message' => 'No ring sudah digunakan',
                    'status_code' => 422,
                    'data' => null,
                ], 422);
            }

            $induk->admin_id = $user->admin->id;
            $induk->no_ring = $request->no_ring;
            $induk->tanggal_lahir = $request->tanggal_lahir;
            $induk->jenis_kelamin = $request->jenis_kelamin;
            $induk->jenis_kenari = $request->jenis_kenari;
            $induk->keterangan = $request->keterangan;

            if ($request->hasFile('gambar_induk')) {
                $path = $request->file('gambar_induk')->store('public/photos');
                $induk->gambar_induk = Storage::url($path);
            }





            $induk->save();

            return response()->json([
                'message' => 'Data induk berhasil ditambahkan',
                'status_code' => 201,
                'data' => $induk,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Internal Server Error: ' . $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    /**
     * Ambil detail induk
     *
     * @authenticated
     * @urlParam id integer required ID induk
     *
     * @response 200 {
     *   "message": "Detail induk berhasil diambil",
     *   "status_code": 200,
     *   "data": {
     *     "id": 1,
     *    "no_ring": "RING-001",
     *   "tanggal_lahir": "2024-01-15",
     *   "jenis_kelamin": "jantan",
     *   "jenis_kenari": "Yorkshire",
     *   "keterangan": "Induk aktif",
     *  "gambar_induk": "/storage/photos/induk1.jpg",
     *   "created_at": "2024-01-15T12:00:00Z",
     *  "updated_at": "2024-01-15T12:00:00Z"
     *  }
     * }
     * @response 404 {
     *   "message": "Data induk tidak ditemukan",
     *   "status_code": 404,
     *   "data": null
     * }
     */
    public function show($id)
    {
        $induk = Induk::find($id);

        if (!$induk) {
            return response()->json([
                'message' => 'Data induk tidak ditemukan',
                'status_code' => 404,
                'data' => null,
            ]);
        }

        return response()->json([
            'message' => 'Detail induk berhasil diambil',
            'status_code' => 200,
            'data' => $induk,
        ]);
    }

    /**
     * Perbarui data induk
     *
     * @authenticated
     * @urlParam id integer required ID induk
     * @bodyParam (semua field sama seperti tambah)
     *
     * @response 200 {
     *   "message": "Data induk berhasil diperbarui",
     *   "status_code": 200,
     *   "data": { ... }
     * }
     * @response 404 {
     *   "message": "Data induk tidak ditemukan",
     *   "status_code": 404,
     *   "data": null
     * }
     */
    public function update($id, StoreIndukRequest $request)
    {
        $induk = Induk::find($id);

        if (!$induk) {
            return response()->json([
                'message' => 'Data induk tidak ditemukan',
                'status_code' => 404,
                'data' => null,
            ]);
        }

        $induk->fill($request->only([
            'no_ring',
            'tanggal_lahir',
            'jenis_kelamin',
            'jenis_kenari',
            'keterangan',
        ]));

        if ($request->hasFile('gambar_induk')) {
            if ($induk->gambar_induk) {
                Storage::delete(str_replace('/storage/', 'public/', $induk->gambar_induk));
            }

            $path = $request->file('gambar_induk')->store('public/photos');
            $induk->gambar_induk = Storage::url($path);
        }

        $induk->save();

        return response()->json([
            'message' => 'Data induk berhasil diperbarui',
            'status_code' => 200,
            'data' => $induk,
        ]);
    }

    /**
     * Hapus data induk
     *
     * @authenticated
     * @urlParam id integer required ID induk
     *
     * @response 200 {
     *   "message": "Data induk berhasil dihapus",
     *   "status_code": 200,
     *   "data": null
     * }
     * @response 404 {
     *   "message": "Data induk tidak ditemukan",
     *   "status_code": 404,
     *   "data": null
     * }
     */
    public function destroy($id)
    {
        $induk = Induk::find($id);

        if (!$induk) {
            return response()->json([
                'message' => 'Data induk tidak ditemukan',
                'status_code' => 404,
                'data' => null,
            ]);
        }

        if ($induk->gambar_induk) {
            Storage::delete(str_replace('/storage/', 'public/', $induk->gambar_induk));
        }

        $induk->delete();

        return response()->json([
            'message' => 'Data induk berhasil dihapus',
            'status_code' => 200,
            'data' => null,
        ]);
    }
}
