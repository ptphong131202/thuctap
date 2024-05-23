<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSvdDiemthiToSvdSinhviendiem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('qlsv_sinhvien_diem', function (Blueprint $table) {
            $table->double('svd_exam_first', 8, 2)->nullable();
            $table->double('svd_exam_second', 8, 2)->nullable();
            $table->double('svd_exam_third', 8, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('svd_sinhviendiem', function (Blueprint $table) {
            //
        });
    }
}
