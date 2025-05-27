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
        Schema::create('induk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->references('id')->on('admin')->onDelete('cascade');
            $table->string('no_ring')->nullable();
            $table->string('gambar_induk')->nullable();
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['jantan', 'betina']);
            $table->string('jenis_kenari');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('induk');
    }
};
