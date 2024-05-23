<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQlsvCanboTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qlsv_canbo', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->increments('cb_id');
            $table->string('cb_ma');
            $table->string('cb_ten');
            $table->string('cb_ho')->nullable();
            $table->string('cb_chucvu')->nullable();
            $table->integer('cb_gioitinh');
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
        Schema::dropIfExists('qlsv_canbo');
    }
}
