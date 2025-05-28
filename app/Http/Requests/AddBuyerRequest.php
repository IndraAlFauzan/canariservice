<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


/**
 * @bodyParam name string required Nama pembeli. Contoh: Budi
 * @bodyParam address string required Alamat pembeli. Contoh: Jl. Merdeka No. 10
 * @bodyParam phone string required Nomor telepon. Contoh: 08123456789
 * @bodyParam photo file Foto (opsional) dalam format jpeg/png/jpg/gif.
 */
class AddBuyerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'    => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone'   => 'required|string|max:15',
            'photo'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}
