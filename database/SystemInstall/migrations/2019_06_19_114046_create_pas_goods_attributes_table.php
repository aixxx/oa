<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasGoodsAttributesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_goods_attributes', function(Blueprint $table)
		{
			$table->increments('goods_attribute_id')->comment('商品属性id自增');
			$table->integer('goods_id')->unsigned()->default(0)->index('goods_id')->comment('商品id');
			$table->integer('attr_id')->unsigned()->default(0)->index('attr_id')->comment('属性id');
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
		Schema::drop('pas_goods_attributes');
	}

}
