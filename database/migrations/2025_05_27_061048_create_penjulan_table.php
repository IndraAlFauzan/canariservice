<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('penjualan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('admin')->onDelete('cascade');
            $table->foreignId('anak_id')->constrained('anak')->onDelete('cascade');
            $table->string('nama_pembeli')->nullable();
            $table->string('no_pembeli')->nullable();
            $table->date('tanggal_transaksi');
            $table->decimal('harga', 10, 2);
            $table->string('gambar_transaksi')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjulan');
    }
};
