<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasAllotCardGoodsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_allot_card_goods', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('allot_id')->nullable()->comment('调拨单id');
			$table->integer('goods_id')->nullable()->comment('商品ID');
			$table->integer('sku_id')->nullable()->comment('sku_id');
			$table->integer('number')->nullable()->comment('调拨数量');
			$table->integer('status')->nullable()->comment('状态');
			$table->softDeletes();
			$table->timestamps();
			$table->integer('warehouse_id')->nullable()->comment('仓库ID');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pas_allot_card_goods');
	}

}
