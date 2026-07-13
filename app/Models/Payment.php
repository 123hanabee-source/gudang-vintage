<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table      = 'payments';
    protected $primaryKey = 'payment_id';
    public $incrementing  = true;
    protected $keyType    = 'int';

    protected $fillable = [
        'order_id', 'payment_method',
        'amount', 'payment_status', 'paid_at',
    ];

    protected $casts = [
        'amount'  => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }
}
