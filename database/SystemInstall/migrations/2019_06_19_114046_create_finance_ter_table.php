<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFinanceTerTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('finance_ter', function(Blueprint $table)
		{
			$table->increments('id')->comment('主键');
			$table->integer('entry_id')->unsigned()->index('idx_entry_id')->comment('流程ID');
			$table->string('title')->comment('标题');
			$table->integer('user_id')->unsigned()->index('idx_user_id')->comment('申请人ID');
			$table->string('user_name', 64)->comment('申请人姓名');
			$table->string('company_name', 45)->comment('所属公司');
			$table->string('department', 45)->comment('所属部门');
			$table->integer('travel_application_id')->comment('出差申请单ID');
			$table->bigInteger('total_amount')->unsigned()->comment('总报销金额，单位分');
			$table->bigInteger('actual_amount')->unsigned()->comment('实际报销金额，单位分');
			$table->string('payment_method', 32)->comment('付款方式');
			$table->string('memo')->default('')->comment('备注');
			$table->text('receive_card_num', 65535)->comment('收款银行卡（已加密）');
			$table->text('receive_card_bank', 65535)->comment('收款银行卡卡行（已加密）');
			$table->integer('file_storage_id')->default(0)->comment('附件ID：无附件则为0');
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
		Schema::drop('finance_ter');
	}

}
