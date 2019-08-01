<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasSaleReturnOrdersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_sale_return_orders', function(Blueprint $table)
		{
			$table->increments('id')->comment('自动编号');
			$table->string('order_sn', 30)->nullable()->comment('销售单号');
			$table->integer('sale_order_id')->nullable()->default(0)->comment('销售单id');
			$table->integer('user_id')->unsigned()->nullable()->default(0)->comment('申请用户编号');
			$table->integer('create_user_id')->unsigned()->nullable()->default(0)->comment('制单人');
			$table->decimal('order_money', 11)->nullable()->default(0.00)->comment('销售单实际总金额');
			$table->decimal('total_money', 11)->nullable()->default(0.00)->comment('本单退款金额');
			$table->decimal('refunded_money', 11)->nullable()->default(0.00)->comment('应退金额');
			$table->decimal('real_refund_money', 11)->nullable()->default(0.00)->comment('实退金额');
			$table->decimal('other_money', 11)->nullable()->default(0.00)->comment('其它费用');
			$table->integer('goods_num')->nullable()->default(0)->comment('销售商品总数量');
			$table->integer('return_num')->nullable()->default(0)->comment('退货数量');
			$table->dateTime('return_time')->nullable()->comment('退货时间');
			$table->dateTime('return_money_time')->nullable()->default('0000-00-00 00:00:00')->comment('预计退款时间');
			$table->dateTime('business_time')->nullable()->comment('业务日期');
			$table->string('remark', 300)->nullable()->comment('备注');
			$table->string('annex')->nullable()->comment('附件');
			$table->boolean('status')->nullable()->comment('状态 0草稿 1审核中 2已撤回 3已退回 4审核完成(待入库) 5部分入库 6入库完成');
			$table->integer('entrise_id')->nullable()->default(0)->comment('审核流程id');
			$table->string('bank_name', 150)->nullable()->comment('开户银行');
			$table->string('subbranch', 150)->nullable()->comment('开户支行');
			$table->string('bank_account', 100)->nullable()->comment('银行账户');
			$table->string('account_holder')->nullable()->comment('开户人');
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
		Schema::drop('pas_sale_return_orders');
	}

}
