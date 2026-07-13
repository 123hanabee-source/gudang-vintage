<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('payment_id');
            $table->unsignedInteger('order_id');
            $table->string('payment_method', 50);
            $table->decimal('amount', 10, 2);
            $table->string('payment_status', 20)->default('Unpaid');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('order_id')
                  ->references('order_id')->on('orders')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
