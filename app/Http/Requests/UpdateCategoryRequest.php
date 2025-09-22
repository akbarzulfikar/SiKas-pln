<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categories', 'category_name')->ignore($this->route('id'), 'category_id')
            ],
            'description' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean'
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
            'category_name.required' => 'Nama kategori harus diisi',
            'category_name.unique' => 'Nama kategori sudah digunakan',
            'category_name.max' => 'Nama kategori maksimal 100 karakter',
            'description.max' => 'Deskripsi maksimal 500 karakter'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'category_name' => 'nama kategori',
            'description' => 'deskripsi'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Handle checkbox value properly
        $this->merge([
            'is_active' => $this->has('is_active') ? true : false
        ]);
    }
}