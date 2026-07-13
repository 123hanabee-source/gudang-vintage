<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'Nama produk wajib diisi.',
            'category.required'  => 'Pilih kategori produk.',
            'category.in'        => 'Kategori tidak valid.',
            'price.required'     => 'Harga wajib diisi.',
            'price.min'          => 'Harga minimal Rp 1.000.',
            'stock.required'     => 'Stok wajib diisi.',
            'stock.min'          => 'Stok tidak boleh negatif.',
            'image.image'        => 'File harus berupa gambar.',
            'image.max'          => 'Ukuran foto maksimal 2MB.',
        ];
    }
}
