<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQlsvDotThiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qlsv_dotthi', function (Blueprint $table) {
            $table->increments('dt_id');
            $table->string('dt_ten')->nullable();
            $table->integer('dt_tungay')->nullable();
            $table->integer('dt_denngay')->nullable();
            $table->integer('dt_tuthang')->nullable();
            $table->integer('dt_denthang')->nullable();
            $table->integer('dt_tunam')->nullable();
            $table->integer('dt_dennam')->nullable();
            $table->integer('dt_loai')->default(0);
            $table->integer('dt_ketthuc')->default(0);
            $table->string('dt_ghichu')->nullable();
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
        Schema::dropIfExists('qlsv_dotthi');
    }
}
