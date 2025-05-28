<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


/**
 * @bodyParam admin_id int ID admin yang membuat data anak. Contoh: 1
 * @bodyParam no_ring string required Nomor ring anak. Contoh: RING123
 * @bodyParam gambar_anak file Gambar anak (opsional) dalam format jpeg/png/jpg.
 * @bodyParam tanggal_lahir date required Tanggal lahir anak. Contoh: 2023-01-01
 * @bodyParam jenis_kelamin string required Jenis kelamin anak (jantan/ betina). Contoh: jantan
 * @bodyParam jenis_kenari string required Jenis kenari anak. Contoh: Kenari Yorkshire
 * @bodyParam keterangan string Keterangan tambahan (opsional). Contoh: Anak sehat dan aktif
 * @bodyParam ayah_id int required ID induk jantan. Contoh: 1
 * @bodyParam ibu_id int required ID induk betina. Contoh: 2
 */

class StoreAnakRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'admin_id' => 'exists:admins,id',
            'no_ring' => 'required|string|max:255|unique:anak,no_ring',
            'gambar_burung' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:jantan,betina',
            'jenis_kenari' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'ayah_id' => 'required|exists:induk,id',
            'ibu_id' => 'required|exists:induk,id',

        ];
    }
}
