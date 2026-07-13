<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $table      = 'order_items';
    protected $primaryKey = 'order_item_id';
    public $incrementing  = true;
    protected $keyType    = 'int';

    protected $fillable = [
        'order_id', 'product_id',
        'product_name', 'product_sku', 'price',
    ];

    protected $casts = ['price' => 'decimal:2'];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
