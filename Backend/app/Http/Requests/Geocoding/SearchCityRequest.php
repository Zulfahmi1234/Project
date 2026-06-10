<?php

namespace App\Http\Requests\Geocoding;

use Illuminate\Foundation\Http\FormRequest;

class SearchCityRequest extends FormRequest
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
            'q'     => ['required', 'string', 'min:2'],
            'count' => ['nullable', 'integer', 'min:1', 'max:10'],
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
            'q.required' => 'Parameter pencarian wajib diisi.',
            'q.min'      => 'Parameter pencarian minimal 2 karakter.',
            'count.integer' => 'Count harus berupa angka.',
            'count.min'  => 'Count minimal 1.',
            'count.max'  => 'Count maksimal 10.',
        ];
    }
}
