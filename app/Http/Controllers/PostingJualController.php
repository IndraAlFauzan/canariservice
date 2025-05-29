<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

namespace App\Http\Controllers;


use App\Models\Anak;
use App\Models\Induk;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StorePostingJualRequest;
use App\Models\PostingJual;

class PostingJualController extends Controller
{
    /**
     * Posting burung untuk dijual
     *
     * @authenticated
     * @bodyParam burung_id integer required ID burung. Contoh: 2
     * @bodyParam burung_type string required Jenis burung: anak atau induk
     * @bodyParam harga integer required Harga burung. Contoh: 150000
     * @bodyParam deskripsi string Deskripsi burung. Contoh: Sehat dan aktif
     *
     * @response 201 {
     *   "message": "Burung berhasil diposting untuk dijual",
     *   "status_code": 201,
     *   "data": {
     *     "id": 1,
     *     "burung_type": "anak",
     *     "burung_id": 2,
     *     "harga": 150000,
     *     "status": "tersedia"
     *   }
     * }
     * 
     * @response 404 {
     *   "message": "Burung tidak ditemukan",
     *   "status_code": 404,
     *   "data": null
     * }
     * @response 422 {
     *   "message": "The burung_id field is required.",
     *   "status_code": 422,
     *   "data": null
     * }
     * @response 500 {
     *   "message": "Internal Server Error: ...",
     *   "status_code": 500,
     *   "data": null
     * }
     */
    public function store(StorePostingJualRequest $request)
    {
        $admin = Auth::guard('api')->user()->admin;

        $model = $request->burung_type === 'anak' ? Anak::class : Induk::class;
        $burung = $model::find($request->burung_id);

        if (!$burung) {
            return response()->json([
                'message' => 'Burung tidak ditemukan',
                'status_code' => 404,
                'data' => null
            ], 404);
        }

        $posting = new PostingJual([
            'harga' => $request->harga,
            'deskripsi' => $request->deskripsi,
            'admin_id' => $admin->id,
            'status' => 'tersedia',
        ]);
        $posting->burung()->associate($burung);
        $posting->save();

        $burung = $posting->burung;
        $tanggal_lahir = $burung?->tanggal_lahir;
        $image = $burung?->gambar_burung ? asset('storage/' . $burung->gambar_burung) : null;

        return response()->json([
            'message' => 'Detail posting ditemukan',
            'status_code' => 201,
            'data' => [
                'id' => $posting->id,
                'image' => $image,
                'no_ring' => $burung?->no_ring ?? '-',
                'jenis_kelamin' => $burung?->jenis_kelamin ?? '-',
                'usia' => $tanggal_lahir ? now()->diffInMonths($tanggal_lahir) . ' bulan' : '-',
                'jenis_kenari' => $burung?->jenis_kenari ?? '-',
                'harga' => $posting->harga,
                'deskripsi' => $posting->deskripsi,
                'status' => $posting->status,
            ]
        ], 201);
    }

    /**
     * Daftar burung yang tersedia untuk dijual
     *
     * @authenticated
     *
     * @response 200 {
     *   "message": "Daftar burung tersedia",
     *   "status_code": 200,
     *   "data": [
     *     {
     *       "id": 1,
     *       "tipe": "Anak",
     *       "no_ring": "123456",
     *       "jenis_kelamin": "Jantan",
     *       "usia": "3 bulan",
     *       "jenis_kenari": "Kenari",
     *       "harga": 150000,
     *       "deskripsi": "Sehat dan aktif",
     *       "status": "tersedia"
     *     }
     *   ]
     * }
     *
     * @response 404 {
     *   "message": "Tidak ada burung yang tersedia untuk dijual",
     *   "status_code": 404,
     *   "data": null
     * }
     *  @response 500 {
     *   "message": "Terjadi kesalahan: ...",
     *   "status_code": 500,
     *   "data": null
     * }
     */

    public function index()
    {
        try {
            $postings = PostingJual::where('status', 'tersedia')->with('burung')->get();

            if ($postings->isEmpty()) {
                return response()->json([
                    'message' => 'Tidak ada burung yang tersedia untuk dijual',
                    'status_code' => 404,
                    'data' => null
                ], 404);
            }

            $data = $postings->map(function ($item) {
                $burung = $item->burung;
                $tanggal_lahir = $burung?->tanggal_lahir;
                $image = $burung?->gambar_burung ? asset('storage/' . $burung->gambar_burung) : null;

                return [
                    'id' => $item->id,
                    'image' => $image,
                    'no_ring' => $burung?->no_ring ?? '-',
                    'jenis_kelamin' => $burung?->jenis_kelamin ?? '-',
                    'usia' => $tanggal_lahir ? now()->diffInMonths($tanggal_lahir) . ' bulan' : '-',
                    'jenis_kenari' => $burung?->jenis_kenari ?? '-',
                    'harga' => $item->harga,
                    'deskripsi' => $item->deskripsi,
                    'status' => $item->status,
                ];
            });

            return response()->json([
                'message' => 'Daftar burung tersedia',
                'status_code' => 200,
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'status_code' => 500,
                'data' => null
            ], 500);
        }
    }

    /**
     * Menampilkan detail posting burung berdasarkan ID
     *
     * @urlParam id integer required ID posting. Contoh: 1
     *
     * @response 200 {
     *   "message": "Detail posting ditemukan",
     *   "status_code": 200,
     *   "data": {
     *     "id": 1,
     *     "tipe": "Anak",
     *     "no_ring": "A1234",
     *     "jenis_kelamin": "jantan",
     *     "usia": "4 bulan",
     *     "jenis_kenari": "kenari",
     *     "harga": 150000,
     *     "deskripsi": "Burung gacor",
     *     "status": "tersedia"
     *   }
     * }
     */
    public function show($id)
    {
        try {
            $posting = PostingJual::with('burung')->find($id);

            if (!$posting) {
                return response()->json([
                    'message' => 'Posting tidak ditemukan',
                    'status_code' => 404,
                    'data' => null,
                ], 404);
            }

            $burung = $posting->burung;
            $tanggal_lahir = $burung?->tanggal_lahir;
            $image = $burung?->gambar_burung ? asset('storage/' . $burung->gambar_burung) : null;

            return response()->json([
                'message' => 'Detail posting ditemukan',
                'status_code' => 200,
                'data' => [
                    'id' => $posting->id,
                    'image' => $image,
                    'no_ring' => $burung?->no_ring ?? '-',
                    'jenis_kelamin' => $burung?->jenis_kelamin ?? '-',
                    'usia' => $tanggal_lahir ? now()->diffInMonths($tanggal_lahir) . ' bulan' : '-',
                    'jenis_kenari' => $burung?->jenis_kenari ?? '-',
                    'harga' => $posting->harga,
                    'deskripsi' => $posting->deskripsi,
                    'status' => $posting->status,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'status_code' => 500,
                'data' => null
            ], 500);
        }
    }

    /**
     * Update status posting burung
     *
     * @urlParam id integer required ID posting. Contoh: 1
     * @bodyParam status string required Status baru: tersedia, terjual, atau ditarik
     *
     * @response 200 {
     *   "message": "Status posting berhasil diperbarui",
     *   "status_code": 200,
     *   "data": {
     *     "id": 1,
     *     "status": "terjual"
     *   }
     * }
     */
    public function updateStatus(StorePostingJualRequest $request, $id)
    {

        try {
            $posting = PostingJual::find($id);

            if (!$posting) {
                return response()->json([
                    'message' => 'Posting tidak ditemukan',
                    'status_code' => 404,
                    'data' => null,
                ], 404);
            }

            $posting->status = $request->status;
            $posting->save();

            return response()->json([
                'message' => 'Status posting berhasil diperbarui',
                'status_code' => 200,
                'data' => [
                    'id' => $posting->id,
                    'status' => $posting->status
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'status_code' => 500,
                'data' => null
            ], 500);
        }
    }

    /**
     * Hapus posting burung
     *
     * @urlParam id integer required ID posting. Contoh: 1
     *
     * @response 200 {
     *   "message": "Posting berhasil dihapus",
     *   "status_code": 200,
     *   "data": null
     * }
     */
    public function destroy($id)
    {
        try {
            $posting = PostingJual::find($id);

            if (!$posting) {
                return response()->json([
                    'message' => 'Posting tidak ditemukan',
                    'status_code' => 404,
                    'data' => null,
                ], 404);
            }

            $posting->delete();

            return response()->json([
                'message' => 'Posting berhasil dihapus',
                'status_code' => 200,
                'data' => null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'status_code' => 500,
                'data' => null
            ], 500);
        }
    }

    public function getAllBurung()
    {
        $anak = Anak::select('id', 'no_ring', 'jenis_kelamin', 'tanggal_lahir', 'jenis_kenari')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'tipe' => 'anak',
                    'no_ring' => $item->no_ring,
                    'jenis_kelamin' => $item->jenis_kelamin,
                    'tanggal_lahir' => $item->tanggal_lahir,
                    'jenis_kenari' => $item->jenis_kenari,
                ];
            });

        $induk = Induk::select('id', 'no_ring', 'jenis_kelamin', 'tanggal_lahir', 'jenis_kenari')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'tipe' => 'induk',
                    'no_ring' => $item->no_ring,
                    'jenis_kelamin' => $item->jenis_kelamin,
                    'tanggal_lahir' => $item->tanggal_lahir,
                    'jenis_kenari' => $item->jenis_kenari,
                ];
            });

        return response()->json([
            'message' => 'Daftar semua burung',
            'status_code' => 200,
            'data' => $anak->merge($induk)->values()
        ]);
    }

    //get all data by status tersedia   
    public function getAllBurungTersedia()
    {
        $postings = PostingJual::where('status', 'tersedia')
            ->with('burung')
            ->get();

        if ($postings->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada burung yang tersedia untuk dijual',
                'status_code' => 404,
                'data' => null
            ], 404);
        }

        $data = $postings->map(function ($item) {
            $burung = $item->burung;
            $tanggal_lahir = $burung?->tanggal_lahir;
            $image = $burung?->gambar_burung ? asset('storage/' . $burung->gambar_burung) : null;

            return [
                'id' => $item->id,
                'image' => $image,
                'no_ring' => $burung?->no_ring ?? '-',
                'jenis_kelamin' => $burung?->jenis_kelamin ?? '-',
                'usia' => $tanggal_lahir ? now()->diffInMonths($tanggal_lahir) . ' bulan' : '-',
                'jenis_kenari' => $burung?->jenis_kenari ?? '-',
                'harga' => $item->harga,
                'deskripsi' => $item->deskripsi,
                'status' => $item->status,
            ];
        });

        return response()->json([
            'message' => 'Daftar burung tersedia',
            'status_code' => 200,
            'data' => $data,
        ], 200);
    }
}
