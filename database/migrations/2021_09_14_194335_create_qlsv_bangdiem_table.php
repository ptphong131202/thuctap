<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQlsvBangdiemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qlsv_bangdiem', function (Blueprint $table) {
            $table->unsignedInteger('lh_id');
            $table->integer('kdt_hocky')->nullable();
            $table->integer('mh_id')->nullable();
            $table->unsignedInteger('user_id');
            $table->increments('bd_id');
            $table->integer('bd_type');
            $table->date('bd_tungay')->nullable();
            $table->date('bd_denngay')->nullable();
            $table->string('bd_giangvien')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('qlsv_bangdiem');
    }
}
