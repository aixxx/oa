<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSalaryRewardPunishmentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('salary_reward_punishment', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('title')->comment('奖励事由');
			$table->integer('admin_id')->comment('申请人ID');
			$table->boolean('type')->comment('奖惩类型 1：奖励 2：惩罚');
			$table->integer('user_id')->comment('成员');
			$table->integer('department_id')->comment('成员');
			$table->integer('money')->comment('奖励金额');
			$table->date('dates')->comment('奖励时间');
			$table->integer('entrise_id')->default(0)->comment('工作流ID');
			$table->boolean('status')->default(0)->comment('审核状态 0：审核中 9：审核通过 -1：驳回');
			$table->timestamps();
			$table->softDeletes()->index()->comment('删除时间');
			$table->integer('task_id')->comment('任务id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('salary_reward_punishment');
	}

}
