<?php
// ── Place all three models in app/Models/
// ── Order.php ────────────────────────────────────────────

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table      = 'orders';
    protected $primaryKey = 'order_id';
    public $incrementing  = true;
    protected $keyType    = 'int';

    protected $fillable = ['customer_id', 'order_date', 'total_amount', 'status'];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'order_date'   => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'order_id', 'order_id');
    }
}
