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
     *       "gambar_burung": "/storage/photos/induk1.jpg",
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
     *      "gambar_burung": "/storage/photos/induk1.jpg",
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
        try {
            $data = Induk::all();

            if ($data->isEmpty()) {
                return response()->json([
                    'message' => 'Tidak ada data induk',
                    'status_code' => 200,
                    'data' => [],
                ], 200);
            }

            $formattedData = $data->map(function ($item) {
                return [
                    'id' => $item->id,
                    'no_ring' => $item->no_ring,
                    'tanggal_lahir' => $item->tanggal_lahir,
                    'jenis_kelamin' => $item->jenis_kelamin,
                    'jenis_kenari' => $item->jenis_kenari,
                    'keterangan' => $item->keterangan,
                    'gambar_burung' => $item->gambar_burung ? asset('storage/' . $item->gambar_burung) : null,
                ];
            });

            return response()->json([
                'message' => 'Data induk berhasil diambil',
                'status_code' => 200,
                'data' => $formattedData,
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
     * @bodyParam gambar_burung file Gambar burung (JPEG/PNG/JPG)
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
     *  "gambar_burung": "/storage/photos/induk1.jpg",
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

            if (Induk::where('no_ring', $request->no_ring)->exists()) {
                return response()->json([
                    'message' => 'No ring sudah digunakan',
                    'status_code' => 422,
                    'data' => null,
                ], 422);
            }

            $induk = new Induk();
            $induk->admin_id = $user->admin->id;
            $induk->no_ring = $request->no_ring;
            $induk->tanggal_lahir = $request->tanggal_lahir;
            $induk->jenis_kelamin = $request->jenis_kelamin;
            $induk->jenis_kenari = $request->jenis_kenari;
            $induk->keterangan = $request->keterangan;

            if ($request->hasFile('gambar_burung')) {
                $path = $request->file('gambar_burung')->store('photos', 'public');
                $induk->gambar_burung = $path;
            }

            $induk->save();

            return response()->json([
                'message' => 'Data induk berhasil ditambahkan',
                'status_code' => 201,
                'data' => [
                    'id' => $induk->id,
                    'no_ring' => $induk->no_ring,
                    'tanggal_lahir' => $induk->tanggal_lahir,
                    'jenis_kelamin' => $induk->jenis_kelamin,
                    'jenis_kenari' => $induk->jenis_kenari,
                    'keterangan' => $induk->keterangan,
                    'gambar_burung' => $induk->gambar_burung ? asset('storage/' . $induk->gambar_burung) : null,
                ]
            ], 201);
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
     *  "gambar_burung": "/storage/photos/induk1.jpg",
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

        if ($request->hasFile('gambar_burung')) {
            if ($induk->gambar_burung) {
                Storage::delete(str_replace('/storage/', 'public/', $induk->gambar_burung));
            }

            $path = $request->file('gambar_burung')->store('public/photos');
            $induk->gambar_burung = Storage::url($path);
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

        if ($induk->gambar_burung) {
            Storage::delete(str_replace('/storage/', 'public/', $induk->gambar_burung));
        }

        $induk->delete();

        return response()->json([
            'message' => 'Data induk berhasil dihapus',
            'status_code' => 200,
            'data' => null,
        ]);
    }
}
