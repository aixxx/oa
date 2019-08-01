<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTransactionLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('transaction_logs', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned()->comment('自动编号');
			$table->integer('user_id')->comment('用户id');
			$table->integer('department_id')->comment('部门id');
			$table->integer('outer_id')->comment('记录外部关联id');
			$table->boolean('is_rpc')->comment('是否通过rpc传输');
			$table->string('model_name', 191)->comment('模型，app/models/文件名');
			$table->string('title', 191)->comment('标题');
			$table->bigInteger('amount')->comment('金额，单位分');
			$table->string('category', 191)->comment('类型：1->报销, 2->借款, 3->还款, 4->收款，5->支付');
			$table->boolean('type')->comment('交易类型, 1=>对内交易（收）, 2=>对内交易（支）, 3 => 对外交易（收）, 4=>对外交易（支），5=>分红支出，6=>资产');
			$table->boolean('is_bill')->comment('是否有单据');
			$table->boolean('is_jysr')->comment('是否是经有收入');
			$table->boolean('in_out')->comment('1=>应收, 2=>应付');
			$table->boolean('is_more_department')->comment('多部门分摊');
			$table->boolean('status')->comment('审核状态：1=>审核通过, 2=>财务付款完成');
			$table->timestamp('status_end_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('审核时间');
			$table->timestamps();
			$table->integer('source')->comment('来源');
			$table->dateTime('status_start_time')->default('0000-00-00 00:00:00')->comment('审核时间');
			$table->integer('company_id')->comment('部门id');
			$table->dateTime('qishu')->default('0000-00-00 00:00:00')->comment('期数');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('transaction_logs');
	}

}
