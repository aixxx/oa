<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFinancialTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('financial', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned()->comment('id');
			$table->string('code', 100)->comment('财务编号');
			$table->integer('flow_id')->comment('流程编号');
			$table->string('title', 100)->nullable();
			$table->integer('user_id')->nullable()->comment('申请人id');
			$table->string('applicant_chinese_name', 50)->nullable()->comment('中文名称');
			$table->integer('company_id')->nullable();
			$table->string('primary_dept', 100)->nullable()->comment('主部门');
			$table->integer('entry_id')->nullable()->comment('申请流程id');
			$table->boolean('status')->nullable()->default(1)->comment('当前状态 -1:审批拒绝 1：待审批 2：批复中 3：审批完成 4：待入账 5：待收支 6：已收支 7：待发票 8：已完成');
			$table->integer('budget_id')->nullable()->comment('预算单id');
			$table->decimal('expense_amount', 10, 0)->nullable();
			$table->string('account_type', 20)->nullable()->comment('账号类型 :支付宝  微信');
			$table->string('account_number', 60)->nullable()->comment('账户账号');
			$table->integer('account_period')->nullable()->comment('账期');
			$table->string('unittype', 20)->nullable()->comment('往来单位类型');
			$table->date('endtime')->nullable()->comment('截止日期');
			$table->string('current_unit', 100)->nullable()->comment('往来单位');
			$table->string('transaction', 100)->nullable()->comment('内外交易:1:对内交易 2：对外交易');
			$table->boolean('fee_booth')->nullable()->default(2)->comment('费用公摊:1:是 2：否');
			$table->boolean('loan_bill')->nullable()->default(2)->comment('借款单:1:是 2：否');
			$table->boolean('associated_projects')->nullable()->default(2)->comment('关联项目:1:是 2：否');
			$table->boolean('linked_order')->nullable()->default(2)->comment('关联订单:1:是 2：否');
			$table->timestamps();
			$table->softDeletes();
			$table->string('reasons', 191)->nullable()->comment('理由');
			$table->integer('loan_bill_id')->nullable()->comment('借款单id');
			$table->integer('projects_id')->nullable()->comment('项目id');
			$table->integer('order_id')->nullable()->comment('订单id');
			$table->decimal('sum_money', 10)->nullable()->default(0.00)->comment('累计金额');
			$table->decimal('cur_money', 10)->nullable()->default(0.00)->comment('当前输入金额');
			$table->boolean('child_status')->nullable()->default(0)->comment('子财务状态');
			$table->dateTime('end_period_at')->default('0000-00-00 00:00:00')->comment('账期截止时间');
			$table->string('bank', 200)->nullable()->comment('开户行');
			$table->string('bank_name', 200)->nullable()->comment('开户名');
			$table->string('bank_address', 200)->nullable()->comment('开户地址');
			$table->string('company_account', 100)->nullable()->comment('还款公司账户');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('financial');
	}

}
