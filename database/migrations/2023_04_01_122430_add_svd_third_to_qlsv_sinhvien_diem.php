<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSvdThirdToQlsvSinhvienDiem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('qlsv_sinhvien_diem', function (Blueprint $table) {
            $table->double('svd_third', 8, 2)->nullable();
            $table->integer('svd_third_hocky')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('qlsv_sinhvien_diem', function (Blueprint $table) {
            //
        });
    }
}
