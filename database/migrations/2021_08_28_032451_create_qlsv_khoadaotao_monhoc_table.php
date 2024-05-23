<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQlsvKhoadaotaoMonhocTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qlsv_khoadaotao_monhoc', function (Blueprint $table) {
            $table->unsignedInteger('kdt_id');
            $table->unsignedInteger('mh_id');
            $table->unsignedInteger('kdt_mh_index');
            $table->unsignedInteger('kdt_mh_hocky');
            $table->primary(['kdt_id', 'mh_id', 'kdt_mh_hocky', 'kdt_mh_index'], 'qlsv_khoadaotao_monhoc_pk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('qlsv_khoadaotao_monhoc');
    }
}
