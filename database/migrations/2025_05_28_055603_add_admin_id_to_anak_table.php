<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdminIdToAnakTable extends Migration
{
    public function up()
    {
        Schema::table('anak', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_id')->after('id');

            $table->foreign('admin_id')
                ->references('id')
                ->on('admin')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('anak', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->dropColumn('admin_id');
        });
    }
}
