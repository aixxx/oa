<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasStockCheckGoodsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_stock_check_goods', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('check_id')->nullable()->comment('盘点id');
			$table->integer('goods_id')->nullable()->comment('商品ID');
			$table->integer('sku_id')->nullable()->comment('skuID');
			$table->integer('number')->nullable()->comment('盘点数');
			$table->integer('profit_loss')->nullable()->comment('盈亏');
			$table->integer('status')->nullable()->comment('状态');
			$table->softDeletes();
			$table->timestamps();
			$table->integer('current_number')->nullable()->comment('当前库存');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pas_stock_check_goods');
	}

}
