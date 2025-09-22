<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'transaction_date' => 'required|date|before_or_equal:today',
            'transaction_type' => 'required|in:income,expense',
            'category_id' => 'required|exists:categories,category_id',
            'amount' => 'required|numeric|min:1|max:999999999',
            'description' => 'nullable|string|max:500',
            'evidence_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120' // 5MB
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
            'transaction_date.required' => 'Tanggal transaksi harus diisi',
            'transaction_date.date' => 'Format tanggal tidak valid',
            'transaction_date.before_or_equal' => 'Tanggal transaksi tidak boleh lebih dari hari ini',
            'transaction_type.required' => 'Jenis transaksi harus dipilih',
            'transaction_type.in' => 'Jenis transaksi harus berupa income atau expense',
            'category_id.required' => 'Kategori harus dipilih',
            'category_id.exists' => 'Kategori yang dipilih tidak valid',
            'amount.required' => 'Jumlah harus diisi',
            'amount.numeric' => 'Jumlah harus berupa angka',
            'amount.min' => 'Jumlah minimal Rp 1',
            'amount.max' => 'Jumlah terlalu besar',
            'description.max' => 'Deskripsi maksimal 500 karakter',
            'evidence_file.file' => 'Bukti harus berupa file',
            'evidence_file.mimes' => 'File harus berformat: jpg, jpeg, png, pdf, doc, docx',
            'evidence_file.max' => 'Ukuran file maksimal 5MB'
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
            'transaction_date' => 'tanggal transaksi',
            'transaction_type' => 'jenis transaksi',
            'category_id' => 'kategori',
            'amount' => 'jumlah',
            'description' => 'deskripsi',
            'evidence_file' => 'file bukti'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean amount from formatting (remove dots, commas etc)
        if ($this->has('amount')) {
            $amount = preg_replace('/[^0-9.]/', '', $this->amount);
            $this->merge([
                'amount' => $amount
            ]);
        }
    }
}