<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQlsvKhoadaotaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qlsv_khoadaotao', function (Blueprint $table) {
            $table->integer('hdt_id');
            $table->integer('nn_id');
            $table->increments('kdt_id');
            $table->string('kdt_ma');
            $table->string('kdt_ten');
            $table->string('kdt_khoa')->nullable();
            $table->string('kdt_he')->nullable();
            $table->integer('kdt_hocky')->default(0);
            $table->text('kdt_ghichu')->nullable();
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
        Schema::dropIfExists('qlsv_khoadaotao');
    }
}
