<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGoodsAllocationGoodsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('goods_allocation_goods', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('allocation_id')->nullable()->comment('货位id');
			$table->integer('goods_id')->nullable()->comment('商品id');
			$table->integer('number')->nullable()->comment('数量');
			$table->softDeletes();
			$table->timestamps();
			$table->integer('sku_id')->nullable()->comment('skuID');
			$table->integer('warehouse_id')->nullable()->default(0)->comment('仓库ID');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('goods_allocation_goods');
	}

}
