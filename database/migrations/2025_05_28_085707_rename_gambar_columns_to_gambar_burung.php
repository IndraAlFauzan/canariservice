<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {


        Schema::table('induk', function (Blueprint $table) {
            $table->renameColumn('gambar_induk', 'gambar_burung');
        });
    }

    public function down()
    {


        Schema::table('induk', function (Blueprint $table) {
            $table->renameColumn('gambar_burung', 'gambar_induk');
        });
    }
};
