<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasPurchaseTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_purchase', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned()->comment('自动编号');
			$table->bigInteger('user_id')->unsigned()->nullable()->comment('申请用户编号');
			$table->string('code', 30)->nullable()->comment('供应商编号');
			$table->string('business_date', 100)->nullable()->comment('业务日期');
			$table->bigInteger('supplier_id')->unsigned()->nullable()->comment('供应商id');
			$table->integer('payable_money')->nullable()->default(0)->comment('此前应付钱');
			$table->string('apply_name', 20)->nullable()->comment('经手人');
			$table->integer('earnest_money')->nullable()->default(0)->comment('定金');
			$table->integer('number')->nullable()->default(0)->comment('商品总数');
			$table->decimal('total_sum', 10)->nullable()->comment('合计金额');
			$table->decimal('discount', 10)->nullable()->default(0.00)->comment('折扣');
			$table->decimal('turnover_amount', 10)->nullable()->comment('成交金额');
			$table->text('remark', 65535)->nullable()->comment('备注');
			$table->boolean('status')->nullable()->default(0)->comment('状态 0草稿 1审核中 2已撤回 3已退回 5审核完成');
			$table->timestamps();
			$table->bigInteger('apply_id')->unsigned()->nullable()->comment('经手人用户编号');
			$table->bigInteger('entrise_id')->unsigned()->nullable()->comment('数据编号id');
			$table->string('supplier_name', 40)->nullable()->comment('供应商名称');
			$table->boolean('p_status')->comment('付款状态 0未付款 1 付款申请中 2付款完成');
			$table->boolean('w_status')->comment('入库状态 0入库未完成 1 入库完成');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pas_purchase');
	}

}
