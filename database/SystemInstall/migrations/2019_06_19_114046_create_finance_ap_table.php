<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFinanceApTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('finance_ap', function(Blueprint $table)
		{
			$table->increments('id')->comment('主键');
			$table->integer('entry_id')->unsigned()->index('idx_entry_id')->comment('流程ID');
			$table->string('title')->comment('标题');
			$table->integer('user_id')->unsigned()->index('idx_user_id')->comment('申请人ID');
			$table->string('user_name', 64)->comment('申请人姓名');
			$table->string('department', 45)->comment('所属部门');
			$table->string('company_name', 64)->comment('所属公司');
			$table->string('payment_method', 32)->comment('付款方式');
			$table->bigInteger('borrow_amount')->unsigned()->comment('借款金额，单位分');
			$table->bigInteger('repay_amount')->unsigned()->default(0)->comment('还款金额，单位分');
			$table->integer('repay_finished')->default(0)->comment('是否还款完成：0-未完成，1-已完成');
			$table->dateTime('repay_finished_at')->default('1000-01-01 00:00:00')->comment('还款完成时间');
			$table->text('cause', 65535)->comment('借款事由');
			$table->text('memo', 65535)->nullable()->comment('借款备注');
			$table->text('receive_card_bank', 65535)->comment('收款银行卡号（加密）');
			$table->text('receive_card_num', 65535)->comment('收款银行卡开户行（加密）');
			$table->integer('file_storage_id')->default(0)->comment('附件');
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
		Schema::drop('finance_ap');
	}

}
