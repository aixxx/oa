<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasWarehouseInGoodsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_warehouse_in_goods', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('in_id')->nullable()->comment('入库单id');
			$table->integer('goods_id')->nullable()->comment('商品id');
			$table->string('goods_no', 191)->nullable()->comment('商品编号');
			$table->integer('sku_id')->nullable()->comment('skuId');
			$table->integer('in_num')->nullable()->comment('申请数');
			$table->integer('stored_num')->nullable()->comment('入库数');
			$table->integer('status')->nullable()->default(0)->comment('状态');
			$table->timestamps();
			$table->integer('warehouse_id')->nullable()->comment('仓库ID');
			$table->integer('type')->nullable()->comment('数据源类型');
			$table->integer('apply_id')->nullable()->comment('申请单ID');
			$table->integer('apply_goods_id')->nullable()->comment('申请单商品主键ID');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pas_warehouse_in_goods');
	}

}
