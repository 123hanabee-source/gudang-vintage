<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:150'],
            'category'    => ['required', 'string', 'in:Baju,Celana,Jaket,Dress,Aksesoris,Sepatu'],
            'brand'       => ['nullable', 'string', 'max:100'],
            'size'        => ['required', 'string', 'in:XS,S,M,L,XL,XXL,Free Size'],
            'condition'   => ['required', 'string', 'in:Like New,Good,Fair'],
            'price'       => ['required', 'numeric', 'min:1000'],
            'stock'       => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:2000'],
            'tags'        => ['nullable', 'string', 'max:255'],
            'status'      => ['required', 'string', 'in:Tersedia,Draft,Habis'],
            // image is optional on update — only validate if a new one is provided
            'image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'Nama produk wajib diisi.',
            'category.required'  => 'Pilih kategori produk.',
            'price.min'          => 'Harga minimal Rp 1.000.',
            'stock.min'          => 'Stok tidak boleh negatif.',
            'image.max'          => 'Ukuran foto maksimal 2MB.',
        ];
    }
}
