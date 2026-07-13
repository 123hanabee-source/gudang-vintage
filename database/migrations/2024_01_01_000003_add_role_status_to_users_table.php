<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom role, status, dan soft delete ke tabel users yang sudah ada.
     * Jalankan ini SETELAH migration users bawaan Laravel.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('Staff')->after('email');    // Admin, Staff, Kasir, Gudang
            $table->string('status')->default('Aktif')->after('role');   // Aktif, Nonaktif
            $table->softDeletes();

            $table->index('role');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['status']);
            $table->dropColumn(['role', 'status']);
            $table->dropSoftDeletes();
        });
    }
};
