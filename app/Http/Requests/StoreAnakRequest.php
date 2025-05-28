<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'gambar_anak' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:jantan,betina',
            'jenis_kenari' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'ayah_id' => 'required|exists:induk,id',
            'ibu_id' => 'required|exists:induk,id',

        ];
    }
}
