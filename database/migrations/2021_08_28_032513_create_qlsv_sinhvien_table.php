<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQlsvSinhvienTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qlsv_sinhvien', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->increments('sv_id');
            $table->string('sv_ma');
            $table->string('sv_ten');
            $table->string('sv_ho')->nullable();
            $table->integer('sv_gioitinh');
            $table->string('sv_dantoc')->nullable();
            $table->string('sv_sdt')->nullable();
            $table->string('sv_diachi')->nullable();
            $table->date('sv_ngaysinh')->nullable();
            $table->string('sv_trinhdo')->nullable();
            $table->date('sv_ngaynghi')->nullable();
            $table->string('sv_ghichu')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('qlsv_sinhvien');
    }
}
