<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function(Blueprint $table){
        	$table->increments('id');
	        $table->string('shop_id');
	        $table->string('order_id')->unique();
	        $table->string('status');
	        $table->string('cost');
	        $table->string('currency');
	        $table->timestamps();
	        $table->dateTime('imported_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('orders');
    }
}
