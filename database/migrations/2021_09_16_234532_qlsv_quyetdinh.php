<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class QlsvQuyetdinh extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qlsv_quyetdinh', function (Blueprint $table) {
            $table->increments('qd_id');
            $table->string('qd_ma');
            $table->string('qd_ten');
            $table->date('qd_ngay');
            $table->integer('qd_loai');
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
        Schema::dropIfExists('qlsv_quyetdinh');
    }
}
