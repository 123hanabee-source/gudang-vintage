<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * GET /api/admin/orders?status=&page=
     * All orders with customer, items, and payment — newest first.
     */
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'items', 'payment'])
                      ->orderByDesc('order_date');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        return $query->paginate(20)->withQueryString();
    }

    /**
     * PATCH /api/admin/payments/{payment}/mark-paid
     * Sets payment_status = Paid and parent order status = Completed.
     */
    public function markPaid(Payment $payment)
    {
        $payment->update([
            'payment_status' => 'Paid',
            'paid_at'        => now(),
        ]);

        $payment->order()->update(['status' => 'Completed']);

        return $payment->load('order');
    }
}
