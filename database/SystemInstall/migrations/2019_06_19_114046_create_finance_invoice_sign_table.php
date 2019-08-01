<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFinanceInvoiceSignTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('finance_invoice_sign', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('applicant_id')->comment('申请人id');
			$table->integer('finance_invoice_sign_entry_id')->comment('发票签收流程id');
			$table->integer('finance_payment_entry_id')->comment('付款流程id');
			$table->text('supplier', 65535)->comment('供应商名称');
			$table->string('invoice_amount_receivable', 191)->comment('应收发票金额');
			$table->string('invoice_num', 191)->comment('发票号');
			$table->string('invoice_type', 191)->comment('发票类型');
			$table->string('invoice_amount', 191)->comment('发票金额,（本次开出发票金额）');
			$table->string('without_tax_amount', 191)->comment('不含税金额');
			$table->string('tax_amount', 191)->comment('税额');
			$table->string('invoice_sheet', 191)->comment('发票张数');
			$table->string('remain_invoice_amount', 191)->comment('剩余发票金额');
			$table->text('remark', 65535)->comment('备注');
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
		Schema::drop('finance_invoice_sign');
	}

}
