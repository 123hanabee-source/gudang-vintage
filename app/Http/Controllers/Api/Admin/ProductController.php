<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // GET /api/admin/products?search=&category=&status=&page=
    public function index(Request $request)
    {
        $query = Product::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name',  'ilike', "%{$search}%")
                  ->orWhere('sku',   'ilike', "%{$search}%")
                  ->orWhere('brand', 'ilike', "%{$search}%")
                  ->orWhere('tags',  'ilike', "%{$search}%");
            });
        }

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        return $query->latest()->paginate(15)->withQueryString();
    }

    // GET /api/admin/products/{product}
    public function show(Product $product)
    {
        return $product;
    }

    // POST /api/admin/products
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'brand'       => 'nullable|string|max:100',
            'category'    => 'required|string|max:100',
            'size'        => 'required|string|max:50',
            'condition'   => 'required|in:Like New,Good,Fair',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'description' => 'nullable|string',
            'tags'        => 'nullable|string',
            'status'      => 'required|in:Tersedia,Draft,Habis',
        ]);

        $data['sku'] = Product::generateSku();

        $product = Product::create($data);

        return response()->json($product, 201);
    }

    // PUT /api/admin/products/{product}
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'        => 'sometimes|required|string|max:255',
            'brand'       => 'nullable|string|max:100',
            'category'    => 'sometimes|required|string|max:100',
            'size'        => 'sometimes|required|string|max:50',
            'condition'   => 'sometimes|required|in:Like New,Good,Fair',
            'price'       => 'sometimes|required|numeric|min:0',
            'stock'       => 'sometimes|required|integer|min:0',
            'description' => 'nullable|string',
            'tags'        => 'nullable|string',
            'status'      => 'sometimes|required|in:Tersedia,Draft,Habis',
        ]);

        // Auto-set status to Habis if stock drops to 0
        if (isset($data['stock']) && (int) $data['stock'] === 0) {
            $data['status'] = 'Habis';
        }

        $product->update($data);

        return $product->fresh();
    }

    // DELETE /api/admin/products/{product}
    public function destroy(Product $product)
    {
        $product->delete(); // soft-delete

        return response()->json(null, 204);
    }

    // PATCH /api/admin/products/{product}/restore  (optional restore)
    public function restore(int $id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        $product->restore();

        return $product;
    }
}
