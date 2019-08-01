<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttendanceWorkflowResumptionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attendance_workflow_resumptions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('entry_id')->nullable()->unique('entry_id_unique')->comment('流程id');
			$table->integer('user_id')->nullable()->index('idx_user_id')->comment('发起人id');
			$table->string('title', 128)->comment('标题');
			$table->integer('resumption_leave_list')->nullable()->unique('resumption_leave_list_unique')->comment('对应已审批请假流程id');
			$table->float('resumption_leave_length', 10, 0)->unsigned()->comment('销假时长');
			$table->timestamps();
			$table->dateTime('finished_at')->nullable()->index('idx_finished_at')->comment('审批完成时间');
			$table->text('resumption_leave_cause', 65535)->nullable()->comment('销假原因');
			$table->integer('file_upload')->default(0)->comment('上传附件');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('attendance_workflow_resumptions');
	}

}
