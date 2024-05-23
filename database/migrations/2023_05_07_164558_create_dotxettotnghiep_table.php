<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDotxettotnghiepTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qlsv_dotxettotnghiep', function (Blueprint $table) {
            $table->increments('dxtn_id');
            $table->string('dxtn_ten')->nullable();
            $table->integer('dxtn_tungay')->nullable();
            $table->integer('dxtn_denngay')->nullable();
            $table->integer('dxtn_tuthang')->nullable();
            $table->integer('dxtn_denthang')->nullable();
            $table->integer('dxtn_tunam')->nullable();
            $table->integer('dxtn_dennam')->nullable();
            $table->string('dxtn_ghichu')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('qlsv_dotthi_dotxettotnghiep', function (Blueprint $table) {
            $table->unsignedInteger('dt_id');
            $table->unsignedInteger('dxtn_id');
            $table->primary(['dt_id', 'dxtn_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('qlsv_dotxettotnghiep');
        Schema::dropIfExists('qlsv_dotthi_dotxettotnghiep');
    }
}
