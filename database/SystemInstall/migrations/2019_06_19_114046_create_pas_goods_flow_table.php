<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasGoodsFlowTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_goods_flow', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('sku_name', 100)->nullable()->comment('商品货号');
			$table->integer('sku_id')->nullable()->comment('商品货号');
			$table->integer('goods_id')->nullable()->comment('商品ID');
			$table->string('card_no', 100)->nullable()->comment('编号');
			$table->integer('warehouse_id')->nullable()->comment('仓库id');
			$table->integer('type')->nullable()->default(0)->comment('申请单类型');
			$table->softDeletes();
			$table->timestamps();
			$table->integer('plan_id')->nullable()->default(0)->comment('入库计划ID（warehouse_in_card id）');
			$table->integer('allocation_id')->nullable()->default(0)->comment('货位ID');
			$table->integer('apply_id')->nullable()->default(0)->comment('申请单ID');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pas_goods_flow');
	}

}
