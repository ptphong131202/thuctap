<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQlsvMonhocTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qlsv_monhoc', function (Blueprint $table) {
            $table->unsignedInteger('hdt_id')->default(0);
            $table->unsignedInteger('nn_id')->default(0);
            $table->increments('mh_id');
            $table->string('mh_ma');
            $table->string('mh_ten');
            $table->integer('mh_sodonvihoctrinh')->nullable();
            $table->integer('mh_sotiet')->nullable();
            $table->integer('mh_tichluy')->default(0);
            $table->string('mh_giangvien')->nullable();
            $table->string('mh_ghichu')->nullable();
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
        Schema::dropIfExists('qlsv_monhoc');
    }
}
