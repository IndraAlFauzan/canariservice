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

class StoreIndukRequest extends FormRequest
{
    public function authorize()
    {
        return true; // atau bisa tambahkan pengecekan role di sini
    }

    public function rules()
    {
        return [
            'admin_id' => 'exists:admin,id',
            'no_ring' => 'nullable|string|max:255|unique:induk,no_ring',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:jantan,betina',
            'jenis_kenari' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'gambar_induk' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }
}
