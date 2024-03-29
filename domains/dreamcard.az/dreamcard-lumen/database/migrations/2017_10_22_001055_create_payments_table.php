<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('card_id')->unsigned();
            $table->foreign('card_id')->references('id')->on('cards')->onDelete('cascade');
//            $table->string('qr_code');
//            $table->integer('package_id')->unsigned();
//            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
            $table->string('payment_source', 30);
            $table->string('payment_key');
            $table->integer('amount');
//            $table->string('currency');
//            $table->dateTime('end_time');
//            $table->decimal('price');
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
        Schema::dropIfExists('payments');
    }
}
