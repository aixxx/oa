<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasSaleOrderGoodsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_sale_order_goods', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('order_id')->nullable()->index('order_id')->comment('销售订单id');
			$table->integer('goods_id')->nullable()->default(0)->index('goods_id')->comment('商品id');
			$table->integer('user_id')->nullable()->comment('客户id');
			$table->integer('sku_id')->nullable()->comment('skuid');
			$table->decimal('cost_price', 10)->nullable()->default(0.00)->comment('成本价格');
			$table->decimal('sale_price', 10)->nullable()->default(0.00)->comment('零售价');
			$table->decimal('wholesale_price', 10)->nullable()->default(0.00)->comment('批发价');
			$table->float('discount', 5)->nullable()->default(100.00)->comment('商品折扣');
			$table->decimal('price', 10)->nullable()->default(0.00)->comment('本单商品实际g购买价格');
			$table->integer('num')->unsigned()->nullable()->default(0)->comment('购买数量');
			$table->integer('out_num')->nullable()->default(0)->comment('出库数量');
			$table->integer('apply_out_num')->nullable()->default(0)->comment('申请中出库数量');
			$table->integer('back_num')->nullable()->default(0)->comment('退货数量');
			$table->integer('apply_back_num')->nullable()->default(0)->comment('申请中退货数量');
			$table->decimal('money', 10)->nullable()->default(0.00)->comment('商品总金额');
			$table->boolean('status')->nullable()->default(0)->comment('订单商品状态');
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
		Schema::drop('pas_sale_order_goods');
	}

}
