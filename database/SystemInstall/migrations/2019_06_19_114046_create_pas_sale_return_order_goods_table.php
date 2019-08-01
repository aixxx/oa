<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasSaleReturnOrderGoodsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_sale_return_order_goods', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('return_order_id')->nullable()->default(0)->comment('退货订单id');
			$table->integer('sale_order_id')->nullable()->index('order_id')->comment('销售订单id');
			$table->integer('sale_order_goods_id')->nullable()->default(0)->comment('销售单商品主键id');
			$table->integer('goods_id')->nullable()->default(0)->index('goods_id')->comment('商品id');
			$table->integer('sku_id')->nullable()->comment('skuid');
			$table->decimal('goods_money', 10)->nullable()->default(0.00)->comment('商品总金额');
			$table->decimal('return_money', 10)->nullable()->default(0.00)->comment('退货金额');
			$table->integer('num')->unsigned()->nullable()->default(0)->comment('购买数量');
			$table->integer('return_num')->nullable()->default(0)->comment('退货数量');
			$table->integer('in_num')->nullable()->default(0)->comment('入库数量');
			$table->integer('apply_in_num')->nullable()->default(0)->comment('申请入库数量');
			$table->boolean('status')->nullable()->default(0)->comment('退货商品状态');
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
		Schema::drop('pas_sale_return_order_goods');
	}

}
