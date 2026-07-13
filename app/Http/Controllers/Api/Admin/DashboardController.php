<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        return response()->json([
            'total_products'   => Product::count(),
            'available'        => Product::where('status', 'Tersedia')->count(),
            'low_stock'        => Product::where('stock', '<=', 2)->where('stock', '>', 0)->count(),
            'out_of_stock'     => Product::where('stock', 0)->count(),
            'total_customers'  => Customer::count(),
            'total_users'      => User::count(),
            'recent_products'  => Product::latest()->take(5)
                                    ->get(['id', 'name', 'sku', 'stock', 'status', 'category']),
            'recent_customers' => Customer::latest()->take(5)
                                    ->get(['id', 'name', 'email', 'address', 'created_at']),
        ]);
    }
}
