<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQlsvDotThiBangdiemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qlsv_dotthi_bangdiem', function (Blueprint $table) {
            $table->unsignedInteger('lh_id');
            $table->integer('mh_id')->nullable();
            $table->integer('dt_id')->nullable();
            $table->integer('dt_bd_lan')->nullable();
            $table->unsignedInteger('userid');
            $table->increments('dt_bd_id');
            $table->date('dt_bd_tungay')->nullable();
            $table->date('dt_bd_denngay')->nullable();
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
        Schema::dropIfExists('qlsv_dotthi_bangdiem');
    }
}
