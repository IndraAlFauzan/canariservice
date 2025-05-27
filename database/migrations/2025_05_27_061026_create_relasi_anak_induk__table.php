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
        Schema::create('relasi_anak_induk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anak_id')->constrained('anak')->onDelete('cascade');
            $table->foreignId('induk_id')->constrained('induk')->onDelete('cascade');
            $table->foreignId('status_induk_id')->constrained('status_induk')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relasi_anak_induk_');
    }
};
