<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasGoodsSpecificsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_goods_specifics', function(Blueprint $table)
		{
			$table->increments('goods_specific_id')->comment('商品规格id自增');
			$table->integer('goods_id')->unsigned()->default(0)->index('goods_id')->comment('商品id');
			$table->integer('spec_id')->unsigned()->default(0)->index('attr_id')->comment('规格id');
			$table->timestamps();
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pas_goods_specifics');
	}

}
