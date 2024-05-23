<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQlsvSinhvienLophocTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qlsv_sinhvien_lophoc', function (Blueprint $table) {
            $table->unsignedInteger('sv_id');
            $table->unsignedInteger('lh_id');
            $table->primary(['sv_id', 'lh_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('qlsv_sinhvien_lophoc');
    }
}
