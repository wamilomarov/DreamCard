<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('status')->default(2); //1 - dreamczard admin, 2 - user
            $table->string('username')->unique();
            $table->string('email')->uniqie();
            $table->string('phone')->unique();
            $table->string('password');
            $table->integer('facebook_id')->nullable()->unique();
            $table->integer('google_id')->nullable()->unique();
            $table->integer('firebase_id')->nullable()->unique();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->integer('photo_id')->nullable()->unsigned();
            $table->foreign('photo_id')->references('id')->on('photos')->onDelete('cascade');
            $table->string('api_token')->nullable();
            $table->integer('get_news')->nullable();
            $table->integer('city_id')->nullable()->unsigned();
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
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
        Schema::dropIfExists('users');
    }
}
