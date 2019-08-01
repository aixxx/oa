<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasSaleReturnInWarehouseTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_sale_return_in_warehouse', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('user_id')->nullable()->default(0)->comment('申请人');
			$table->string('in_sn')->nullable()->comment('出库单申请编号');
			$table->integer('sale_order_id')->nullable()->default(0)->index('order_id')->comment('销售订单id');
			$table->string('sale_order_sn', 50)->nullable()->comment('销售订单编号');
			$table->integer('return_order_id')->nullable()->default(0)->comment('退货单id');
			$table->string('return_order_sn', 50)->nullable()->comment('退货单编号');
			$table->integer('num')->unsigned()->nullable()->default(0)->comment('销售商品总数量');
			$table->integer('in_num')->nullable()->default(0)->comment('入库数量');
			$table->dateTime('in_time')->nullable()->comment('入库时间');
			$table->integer('shipping_id')->nullable()->default(0)->comment('物流id');
			$table->boolean('status')->nullable()->default(0)->comment('申请单状态 状态 0草稿 1审核中 2已撤回 3已驳回 4审核完成');
			$table->integer('entrise_id')->nullable()->default(0)->comment('审核流id');
			$table->string('remark')->nullable()->comment('备注');
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pas_sale_return_in_warehouse');
	}

}
