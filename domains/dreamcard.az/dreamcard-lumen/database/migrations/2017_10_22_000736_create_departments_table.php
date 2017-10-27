<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('partner_id')->unsigned();
            $table->foreign('partner_id')->references('id')->on('partners')->onDelete('cascade');
            $table->integer('photo_id')->unsigned();
            $table->foreign('photo_id')->references('id')->on('photos')->onDelete('cascade');
            $table->integer('city_id')->unsigned();
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
//            $table->float('lat');
//            $table->float('lng');
            $table->string('username')->unique();
            $table->string('password');
            $table->string('api_token');
            $table->tinyInteger('first_entry')->deafult(1);
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
        Schema::dropIfExists('departments');
    }
}
