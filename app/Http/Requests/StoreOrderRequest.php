<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreOrderRequest extends FormRequest
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
            'pickup_date' => [
                'required',
                'date',
                'after_or_equal:today',
                'before:' . now()->addMonths(1)->format('Y-m-d'), // Max 1 month ahead
            ],
            'payment_proof' => [
                'required',
                'image',
                'mimes:jpeg,jpg,png,webp',
                'max:2048', // 2MB
                'dimensions:min_width=100,min_height=100,max_width=5000,max_height=5000'
            ],
            'phone' => 'nullable|string|min:10|max:15|regex:/^[0-9]+$/',
            'notes' => 'nullable|string|max:500'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'pickup_date.required' => 'Tanggal pengambilan wajib diisi.',
            'pickup_date.after_or_equal' => 'Tanggal pengambilan minimal hari ini.',
            'pickup_date.before' => 'Tanggal pengambilan maksimal 1 bulan ke depan.',
            'payment_proof.required' => 'Bukti pembayaran wajib diupload.',
            'payment_proof.image' => 'File harus berupa gambar.',
            'payment_proof.mimes' => 'Format gambar harus JPEG, JPG, PNG, atau WEBP.',
            'payment_proof.max' => 'Ukuran file maksimal 2MB.',
            'payment_proof.dimensions' => 'Dimensi gambar minimal 100x100px dan maksimal 5000x5000px.',
            'phone.min' => 'Nomor telepon minimal 10 digit.',
            'phone.max' => 'Nomor telepon maksimal 15 digit.',
            'phone.regex' => 'Nomor telepon hanya boleh berisi angka.',
            'notes.max' => 'Catatan maksimal 500 karakter.',
        ];
    }
}
