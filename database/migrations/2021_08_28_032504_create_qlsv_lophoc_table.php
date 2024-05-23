<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQlsvLophocTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qlsv_lophoc', function (Blueprint $table) {
            $table->unsignedInteger('kdt_id');
            $table->unsignedInteger('nk_id');
            $table->unsignedInteger('qd_id');
            $table->increments('lh_id');
            $table->string('lh_ma');
            $table->string('lh_ten');
            $table->text('lh_ghichu')->nullable();
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
        Schema::dropIfExists('qlsv_lophoc');
    }
}
