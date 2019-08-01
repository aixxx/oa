<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasSaleOrdersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_sale_orders', function(Blueprint $table)
		{
			$table->increments('id')->comment('自动编号');
			$table->string('order_sn', 30)->nullable()->comment('销售单号');
			$table->integer('user_id')->unsigned()->nullable()->default(0)->comment('申请用户编号');
			$table->integer('buy_user_id')->unsigned()->nullable()->default(0)->comment('销售的客户');
			$table->integer('goods_num')->nullable()->default(0)->comment('销售的商品总数');
			$table->decimal('goods_money', 11)->nullable()->default(0.00)->comment('商品总金额');
			$table->decimal('total_money', 11)->nullable()->default(0.00)->comment('此单金额');
			$table->decimal('receivable_money', 11)->nullable()->default(0.00)->comment('应收金额');
			$table->decimal('actual_money', 11)->nullable()->default(0.00)->comment('实收金额');
			$table->decimal('zero_money', 11)->nullable()->default(0.00)->comment('抹零金额');
			$table->decimal('other_money', 11)->nullable()->default(0.00)->comment('其它费用金额');
			$table->float('discount', 5)->nullable()->default(100.00)->comment('订单折扣');
			$table->dateTime('expected_pay_time')->nullable()->comment('预计付款时间');
			$table->string('bank_name', 150)->nullable()->comment('开户银行');
			$table->string('subbranch', 150)->nullable()->comment('开户支行');
			$table->string('bank_account', 100)->nullable()->comment('银行账户');
			$table->string('account_holder')->nullable()->comment('开户人');
			$table->dateTime('business_time')->nullable()->comment('业务日期');
			$table->integer('account_period')->nullable()->default(0)->comment('账期 (单位天)');
			$table->integer('sale_user_id')->nullable()->default(0)->comment('销售员');
			$table->integer('invoice_id')->nullable()->default(0)->comment('发货方式');
			$table->string('remark', 300)->nullable()->comment('备注');
			$table->string('annex')->nullable()->comment('附件');
			$table->boolean('status')->nullable()->comment('状态 0草稿 1审核中 2已撤回 3已退回 4审核完成(待出库) 5部分出库 6出库完成');
			$table->integer('entrise_id')->nullable()->default(0)->comment('审核流程id');
			$table->timestamps();
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pas_sale_orders');
	}

}
