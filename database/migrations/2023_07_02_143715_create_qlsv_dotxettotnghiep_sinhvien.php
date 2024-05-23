<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQlsvDotxettotnghiepSinhvien extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::create('qlsv_dotxettotnghiep_sinhvien', function (Blueprint $table) {
            $table->increments('sv_id')->primary();
            $table->unsignedInteger('dxtn_id');
            $table->unsignedInteger('dt_id')->unsigned();
            $table->unsignedInteger('lh_id')->unsigned();
            $table->unsignedInteger('svxtn_dattn')->unsigned();
            $table->string('svxtn_ghichu')->nullable();
            $table->primary(['sv_id', 'dxtn_id']);
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */


    public function down()
    {
        Schema::dropIfExists('qlsv_dotxettotnghiep_sinhvien');
    }
}
