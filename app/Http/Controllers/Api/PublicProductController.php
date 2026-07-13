<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class PublicProductController extends Controller
{
    /**
     * GET /api/products
     * Public catalog feed — only available (status=Tersedia, stock>0) items.
     */
    public function index(Request $request)
    {
        $query = Product::where('status', 'Tersedia')
                        ->where('stock', '>', 0);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name',     'ilike', "%{$search}%")
                  ->orWhere('brand',  'ilike', "%{$search}%")
                  ->orWhere('tags',   'ilike', "%{$search}%");
            });
        }

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        return $query->latest()->get([
            'id', 'name', 'sku', 'brand', 'category',
            'size', 'condition', 'price', 'stock',
            'description', 'tags', 'status',
        ]);
    }

    /**
     * GET /api/products/{product}
     * Single product detail.
     */
    public function show(Product $product)
    {
        if ($product->status !== 'Tersedia' || $product->stock <= 0) {
            return response()->json(['message' => 'Produk tidak tersedia.'], 404);
        }
        return $product;
    }

    /**
     * GET /api/categories
     * Distinct categories for the filter pills on the storefront.
     */
    public function categories()
    {
        return Product::where('status', 'Tersedia')
                      ->distinct()
                      ->pluck('category')
                      ->filter()
                      ->values();
    }
}
