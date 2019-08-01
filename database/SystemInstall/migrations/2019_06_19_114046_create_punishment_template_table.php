<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePunishmentTemplateTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('punishment_template', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned()->comment('自动编号');
			$table->bigInteger('user_id')->unsigned()->nullable()->comment('用户编号');
			$table->bigInteger('company_id')->unsigned()->nullable()->comment('公司编号');
			$table->string('title', 100)->nullable()->comment('惩罚模板名称');
			$table->integer('penalty_multiple')->nullable()->default(0)->comment('旷工扣除薪资倍数');
			$table->boolean('status')->nullable()->default(1)->comment('0已删除  1正常');
			$table->timestamps();
			$table->boolean('type')->default(1)->comment('类型 1表示加班费 2迟到扣费 3旷工扣费 4请假');
			$table->boolean('types')->default(1)->comment('小类型区分');
			$table->boolean('overtime_type')->default(1)->comment('加班类型1 工作日加班 2休息日加班  3节假日加班');
			$table->decimal('money', 10)->nullable()->comment('支付金额 扣款金额（有百分比有直接是金额）');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('punishment_template');
	}

}
