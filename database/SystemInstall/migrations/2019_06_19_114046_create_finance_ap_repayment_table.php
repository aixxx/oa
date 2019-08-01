<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFinanceApRepaymentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('finance_ap_repayment', function(Blueprint $table)
		{
			$table->increments('id')->comment('主键');
			$table->integer('finance_ap_id')->unsigned()->index('idx_finance_ap_id')->comment('财务暂支单ID');
			$table->bigInteger('repay_amount')->unsigned()->comment('还款金额，单位为分');
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
		Schema::drop('finance_ap_repayment');
	}

}
