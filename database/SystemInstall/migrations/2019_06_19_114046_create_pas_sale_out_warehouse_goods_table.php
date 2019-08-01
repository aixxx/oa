<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasSaleOutWarehouseGoodsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_sale_out_warehouse_goods', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('out_id')->nullable()->default(0)->index('order_id')->comment('销售订单id');
			$table->integer('sale_order_goods_id')->nullable()->default(0)->comment('销售单商品表主键id');
			$table->integer('goods_id')->nullable()->default(0)->index('goods_id')->comment('商品id');
			$table->integer('sku_id')->nullable()->default(0)->comment('skuid');
			$table->integer('out_num')->nullable()->default(0)->comment('出库数量');
			$table->integer('has_out_num')->nullable()->default(0)->comment('仓库实际已出库数量');
			$table->boolean('status')->nullable()->default(1)->comment('状态 0删除 1正常');
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
		Schema::drop('pas_sale_out_warehouse_goods');
	}

}
