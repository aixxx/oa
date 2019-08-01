<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasSaleOutWarehouseTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_sale_out_warehouse', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('user_id')->nullable()->default(0)->comment('申请人');
			$table->string('out_sn')->nullable()->comment('出库单申请编号');
			$table->integer('order_id')->nullable()->default(0)->index('order_id')->comment('销售订单id');
			$table->integer('order_sn')->nullable()->comment('销售订单编号');
			$table->integer('num')->unsigned()->nullable()->default(0)->comment('销售商品总数量');
			$table->integer('out_num')->nullable()->default(0)->comment('商品出库总数量');
			$table->dateTime('out_time')->nullable()->comment('出库时间');
			$table->integer('shipping_id')->nullable()->default(0)->comment('物流id');
			$table->boolean('status')->nullable()->default(0)->comment('申请单状态 状态 0草稿 1审核中 2已撤回 3已驳回 4审核完成');
			$table->integer('entrise_id')->nullable()->default(0)->comment('审批流id');
			$table->string('remark')->nullable()->comment('备注');
			$table->timestamps();
			$table->integer('warehouse_id')->nullable()->comment('仓库ID');
			$table->integer('out_status')->nullable()->comment('出库状态');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pas_sale_out_warehouse');
	}

}
