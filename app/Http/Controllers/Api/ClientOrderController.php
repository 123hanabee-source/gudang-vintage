<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ClientOrderController extends Controller
{
    /**
     * POST /api/orders
     * Body: { customer_id, payment_method, product_ids: [1, 2, ...] }
     *
     * Creates order + order_items + payment atomically.
     * Reduces stock for each product; sets status to "Habis" if stock hits 0.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id'    => 'required|integer|exists:customers,id',
            'payment_method' => 'required|string|max:50',
            'product_ids'    => 'required|array|min:1',
            'product_ids.*'  => 'integer|distinct',
        ]);

        $order = DB::transaction(function () use ($data) {

            // Lock selected products to prevent race conditions
            $products = Product::whereIn('id', $data['product_ids'])
                ->where('status', 'Tersedia')
                ->where('stock', '>', 0)
                ->lockForUpdate()
                ->get();

            if ($products->count() !== count($data['product_ids'])) {
                $found   = $products->pluck('id')->toArray();
                $missing = array_diff($data['product_ids'], $found);
                throw ValidationException::withMessages([
                    'product_ids' => 'Beberapa produk sudah habis atau tidak tersedia: ID ' . implode(', ', $missing),
                ]);
            }

            $total = $products->sum('price');

            // Create order
            $order = Order::create([
                'customer_id'  => $data['customer_id'],
                'order_date'   => now(),
                'total_amount' => $total,
                'status'       => 'Pending',
            ]);

            // Create order items & reduce stock
            foreach ($products as $product) {
                OrderItem::create([
                    'order_id'     => $order->order_id,
                    'product_id'   => $product->id,
                    'product_name' => $product->name,
                    'product_sku'  => $product->sku,
                    'price'        => $product->price,
                ]);

                $newStock = $product->stock - 1;
                $product->update([
                    'stock'  => $newStock,
                    'status' => $newStock <= 0 ? 'Habis' : $product->status,
                ]);
            }

            // Create payment record
            Payment::create([
                'order_id'       => $order->order_id,
                'payment_method' => $data['payment_method'],
                'amount'         => $total,
                'payment_status' => 'Unpaid',
                'paid_at'        => null,
            ]);

            return $order;
        });

        return response()->json(
            $order->load(['items', 'payment']),
            201
        );
    }

    /**
     * GET /api/customer/orders?customer_id=1
     * Returns full order history for a customer with items and payment.
     */
    public function history(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|integer|exists:customers,id',
        ]);

        $orders = Order::with(['items', 'payment'])
            ->where('customer_id', $request->customer_id)
            ->orderByDesc('order_date')
            ->get();

        // Shape the response to match what app.html expects
        return $orders->map(function ($order) {
            return [
                'order_id'     => $order->order_id,
                'order_date'   => $order->order_date,
                'status'       => $order->status,
                'total_amount' => $order->total_amount,
                'items'        => $order->items->map(fn($i) => [
                    'product_name' => $i->product_name,
                    'price'        => $i->price,
                ]),
                'payment' => $order->payment ? [
                    'payment_method'  => $order->payment->payment_method,
                    'payment_status'  => $order->payment->payment_status,
                    'paid_at'         => $order->payment->paid_at,
                ] : null,
            ];
        });
    }
}
