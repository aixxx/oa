<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVacationExtraWorkflowPassTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('vacation_extra_workflow_pass', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('begin_end_dates')->comment('已审批为准加班日期');
			$table->integer('times')->comment('加班时间');
			$table->integer('user_id')->comment('用户ID');
			$table->integer('entry_id')->comment('工作流ID');
			$table->softDeletes();
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
		Schema::drop('vacation_extra_workflow_pass');
	}

}
