<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQlsvSinhvienDiemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qlsv_sinhvien_diem', function (Blueprint $table) {
            $table->unsignedInteger('sv_id');
            $table->unsignedInteger('bd_id');
            $table->unsignedInteger('svd_dulop')->nullable();
            $table->double('svd_first', 8, 2)->nullable();
            $table->double('svd_second', 8, 2)->nullable();
            $table->integer('svd_second_hocky')->nullable();
            $table->double('svd_final', 8, 2)->nullable();
            $table->double('svd_total', 8, 2)->nullable();
            $table->text('svd_ghichu')->nullable();
            $table->primary(['sv_id', 'bd_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('qlsv_sinhvien_diem');
    }
}
