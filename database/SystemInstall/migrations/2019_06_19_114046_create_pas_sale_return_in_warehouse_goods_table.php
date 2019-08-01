<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasSaleReturnInWarehouseGoodsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_sale_return_in_warehouse_goods', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('in_id')->nullable()->default(0)->index('order_id')->comment('入库订单id');
			$table->integer('return_order_goods_id')->nullable()->default(0)->comment('销售单退货商品表主键id');
			$table->integer('goods_id')->nullable()->default(0)->index('goods_id')->comment('商品id');
			$table->integer('sku_id')->nullable()->default(0)->comment('skuid');
			$table->integer('in_num')->nullable()->default(0)->comment('入库数量');
			$table->integer('has_in_num')->nullable()->default(0)->comment('仓库实际入库数量');
			$table->boolean('status')->nullable()->default(0)->comment('状态（备用）');
			$table->timestamps();
			$table->index(['goods_id','sku_id'], 'gk');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pas_sale_return_in_warehouse_goods');
	}

}
