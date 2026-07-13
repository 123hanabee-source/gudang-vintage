<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('sku')->unique();
            $table->string('category');          // Baju, Celana, Jaket, dll
            $table->string('brand')->nullable();
            $table->string('size');              // XS, S, M, L, XL, XXL, Free Size
            $table->string('condition');         // Like New, Good, Fair
            $table->decimal('price', 12, 2);
            $table->unsignedInteger('stock')->default(1);
            $table->text('description')->nullable();
            $table->string('tags')->nullable();  // comma-separated
            $table->string('status')->default('Tersedia'); // Tersedia, Draft, Habis
            $table->string('image_path')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indeks yang sering difilter
            $table->index('category');
            $table->index('status');
            $table->index('stock');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
