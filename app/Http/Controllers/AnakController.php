<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnakRequest;
use App\Models\Anak;
use App\Models\Induk;
use App\Models\RelasiAnakInduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AnakController extends Controller
{
    public function index()
    {

        // Transform the anak data to include only the necessary fields

        try {
            $anakList = Anak::with(['ayah', 'ibu'])->get();

            if ($anakList->isEmpty()) {
                return response()->json([
                    'message' => 'Tidak ada data anak yang ditemukan',
                    'status_code' => 404,
                    'data' => []
                ]);
            }

            $data = $anakList->map(function ($anak) {
                return [
                    'id' => $anak->id,
                    'no_ring' => $anak->no_ring,
                    'tanggal_lahir' => $anak->tanggal_lahir,
                    'jenis_kelamin' => $anak->jenis_kelamin,
                    'jenis_kenari' => $anak->jenis_kenari,
                    'ayah_no_ring' => optional($anak->ayah->first())->no_ring,
                    'ibu_no_ring' => optional($anak->ibu->first())->no_ring,
                ];
            });

            return response()->json([
                'message' => 'Data anak berhasil diambil',
                'status_code' => 200,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error: ' . $e->getMessage(),
                'status_code' => 500,
                'data' => null
            ]);
        }
    }

    public function store(StoreAnakRequest $request)
    {

        try {
            $user = Auth::guard('api')->user();

            if (Anak::where('no_ring', $request->no_ring)->exists()) {
                return response()->json([
                    'message' => 'Anak dengan nomor ring ini sudah ada',
                    'status_code' => 409,
                    'data' => null
                ], 409);
            }
            $anak = new Anak();
            $anak->admin_id = $user->admin->id;
            $anak->no_ring = $request->no_ring;
            $anak->tanggal_lahir = $request->tanggal_lahir;
            $anak->jenis_kelamin = $request->jenis_kelamin;
            $anak->jenis_kenari = $request->jenis_kenari;
            if ($request->hasFile('gambar_anak')) {
                $path = $request->file('gambar_anak')->store('public/photos');
                $anak->gambar_anak = Storage::url($path);
            }

            $anak->save();

            $relasiAyahAnakInduk = new RelasiAnakInduk();
            $relasiAyahAnakInduk->anak_id = $anak->id;
            $relasiAyahAnakInduk->induk_id = $request->ayah_id;
            $relasiAyahAnakInduk->status_induk_id = 1; // Ayah
            $relasiAyahAnakInduk->save();

            $relasiIbuAnakInduk = new RelasiAnakInduk();
            $relasiIbuAnakInduk->anak_id = $anak->id;
            $relasiIbuAnakInduk->induk_id = $request->ibu_id;
            $relasiIbuAnakInduk->status_induk_id = 2; // Ibu
            $relasiIbuAnakInduk->save();
            // RelasiAnakInduk::create([
            //     'anak_id' => $anak->id,
            //     'induk_id' => $request->ayah_id,
            //     'status_induk_id' => 1, // Ayah

            // ]);

            // RelasiAnakInduk::create([
            //     'anak_id' => $anak->id,
            //     'induk_id' => $request->ibu_id,
            //     'status_induk_id' => 2, // Ibu
            // ]);

            $induks = Induk::whereIn('id', [$request->ayah_id, $request->ibu_id])->get()->keyBy('id');

            $ayah = $induks[$request->ayah_id] ?? null;
            $ibu = $induks[$request->ibu_id] ?? null;

            if (!$ayah || !$ibu) {
                return response()->json([
                    'message' => 'Data ayah atau ibu tidak ditemukan',
                    'status_code' => 404,
                    'data' => null
                ], 404);
            }

            return response()->json([
                'message' => 'Data anak berhasil ditambahkan',
                'status_code' => 201,
                'data' => [
                    'id' => $anak->id,
                    'no_ring' => $anak->no_ring,
                    'tanggal_lahir' => $anak->tanggal_lahir,
                    'jenis_kelamin' => $anak->jenis_kelamin,
                    'jenis_kenari' => $anak->jenis_kenari,
                    'gambar_anak' => $anak->gambar_anak,
                    'ayah_no_ring' => $ayah ? $ayah->no_ring : null,
                    'ibu_no_ring' => $ibu ? $ibu->no_ring : null,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error: ' . $e->getMessage(),
                'status_code' => 500,
                'data' => null
            ], 500);
        }
    }
}
