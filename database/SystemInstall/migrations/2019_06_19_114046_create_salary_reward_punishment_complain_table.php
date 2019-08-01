<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSalaryRewardPunishmentComplainTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('salary_reward_punishment_complain', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('pr_id')->comment('奖惩ID');
			$table->string('remark')->comment('申诉理由');
			$table->string('remark_img')->nullable()->comment('图片');
			$table->timestamps();
			$table->softDeletes()->index()->comment('删除时间');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('salary_reward_punishment_complain');
	}

}
