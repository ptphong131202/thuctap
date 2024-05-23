<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQlsvSinhvienQuyetdinh extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qlsv_sinhvien_quyetdinh', function (Blueprint $table) {
            $table->unsignedInteger('sv_id');
            $table->unsignedInteger('qd_id');
            $table->string('qd_hocky')->nullable();
            $table->primary(['sv_id', 'qd_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('qlsv_sinhvien_quyetdinh');
    }
}
