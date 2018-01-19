<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name_en');
            $table->string('name_az');
            $table->string('name_ru');
            $table->integer('small_icon_id')->unsigned();
            $table->foreign('small_icon_id')->references('id')->on('photos')->onDelete('cascade');
            $table->integer('large_icon_id')->unsigned();
            $table->foreign('large_icon_id')->references('id')->on('photos')->onDelete('cascade');
            $table->integer('order_by');
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
        Schema::dropIfExists('categories');
    }
}
