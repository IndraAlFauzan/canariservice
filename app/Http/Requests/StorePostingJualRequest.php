<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @bodyParam no_ring string Nomor ring (opsional). Contoh: R12345
 * @bodyParam tanggal_lahir date Tanggal lahir induk. Contoh: 2020-01-01
 * @bodyParam jenis_kelamin string Jenis kelamin (jantan/ betina). Contoh: jantan
 * @bodyParam jenis_kenari string Jenis kenari. Contoh: Yorkshire
 * @bodyParam keterangan string Keterangan tambahan (opsional). Contoh: Induk sehat
 * @bodyParam gambar_induk file Gambar induk (opsional) dalam format jpeg/png/jpg.
 */

class StorePostingJualRequest extends FormRequest
{
    public function authorize()
    {
        return true; // pastikan middleware auth menangani
    }

    public function rules()
    {
        return [
            'burung_id' => 'required|integer',
            'burung_type' => 'required|in:anak,induk',
            'harga' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'burung_id.required' => 'ID burung wajib diisi',
            'burung_type.in' => 'Tipe burung harus anak atau induk',
        ];
    }
}
