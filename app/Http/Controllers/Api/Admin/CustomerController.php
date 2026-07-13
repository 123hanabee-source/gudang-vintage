<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // GET /api/admin/customers?search=&page=
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name',  'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%")
                  ->orWhere('phone', 'ilike', "%{$search}%");
            });
        }

        return $query->latest()->paginate(20)->withQueryString();
    }

    // GET /api/admin/customers/{customer}
    public function show(Customer $customer)
    {
        return $customer;
    }

    // POST /api/admin/customers
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:100|unique:customers,email',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        return response()->json(Customer::create($data), 201);
    }

    // PUT /api/admin/customers/{customer}
    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name'    => 'sometimes|required|string|max:100',
            'email'   => 'sometimes|required|email|max:100|unique:customers,email,' . $customer->id,
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $customer->update($data);

        return $customer->fresh();
    }

    // DELETE /api/admin/customers/{customer}
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return response()->json(null, 204);
    }
}
