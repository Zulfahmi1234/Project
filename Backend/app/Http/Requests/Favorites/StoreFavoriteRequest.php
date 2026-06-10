<?php

namespace App\Http\Requests\Favorites;

use Illuminate\Foundation\Http\FormRequest;

class StoreFavoriteRequest extends FormRequest
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
            'city_name'    => ['required', 'string', 'max:255'],
            'latitude'     => ['required', 'numeric', 'between:-90,90'],
            'longitude'    => ['required', 'numeric', 'between:-180,180'],
            'country'      => ['required', 'string', 'max:255'],
            'country_code' => ['required', 'string', 'max:10'],
            'timezone'     => ['required', 'string', 'max:100'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'city_name.required'    => 'Nama kota wajib diisi.',
            'latitude.required'     => 'Latitude wajib diisi.',
            'latitude.numeric'      => 'Latitude harus berupa angka.',
            'longitude.required'    => 'Longitude wajib diisi.',
            'longitude.numeric'     => 'Longitude harus berupa angka.',
            'country.required'      => 'Negara wajib diisi.',
            'country_code.required' => 'Kode negara wajib diisi.',
            'timezone.required'     => 'Timezone wajib diisi.',
        ];
    }
}
