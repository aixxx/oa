<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFinanceWorkflowPaymentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('finance_workflow_payment', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('entry_id')->comment('付款申请流程id');
			$table->text('title', 65535)->comment('流程标题');
			$table->text('beneficiary', 65535)->comment('供应商名称');
			$table->string('payment_amount_transfer', 191)->comment('付款金额（总的应收发票金额（元））');
			$table->string('paid_payment_amount_transfer', 191)->comment('已开发票金额');
			$table->text('company', 65535)->comment('所属公司');
			$table->text('primary_dept', 65535)->comment('所属部门');
			$table->text('applicant_chinese_name', 65535)->comment('申请人');
			$table->text('contract_no', 65535)->comment('合同编号');
			$table->text('our_main_body', 65535)->comment('我方主体');
			$table->timestamps();
			$table->integer('applicant_id')->comment('申请人id');
			$table->text('invoice_description', 65535)->comment('发票说明');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('finance_workflow_payment');
	}

}
