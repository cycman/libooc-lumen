<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ZhBookTitleDesc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('b_book_zh_imf', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('bid')->index();
            $table->string('md5')->index();
            $table->text('descr');
            $table->text('title');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('b_book_zh_imf');
    }
}
