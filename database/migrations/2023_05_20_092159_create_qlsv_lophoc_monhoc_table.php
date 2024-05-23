<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQlsvLophocMonhocTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qlsv_lophoc_monhoc', function (Blueprint $table) {
            $table->unsignedInteger('lh_id');
            $table->unsignedInteger('mh_id');
            $table->unsignedInteger('lh_mh_index');
            $table->unsignedInteger('lh_mh_hocky');
            $table->primary(['lh_id', 'mh_id', 'lh_mh_hocky', 'lh_mh_index'], 'qlsv_lophoc_monhoc_pk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('qlsv_lophoc_monhoc');
    }
}
