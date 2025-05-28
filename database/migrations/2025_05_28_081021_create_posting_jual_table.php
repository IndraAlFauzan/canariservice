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
        Schema::create('posting_jual', function (Blueprint $table) {
            $table->id();
            $table->morphs('burung'); // burung_id & burung_type
            $table->foreignId('admin_id')->constrained('admin')->onDelete('cascade');
            $table->integer('harga');
            $table->text('deskripsi')->nullable();
            $table->enum('status', ['tersedia', 'terjual', 'ditarik'])->default('tersedia');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posting_jual');
    }
};
