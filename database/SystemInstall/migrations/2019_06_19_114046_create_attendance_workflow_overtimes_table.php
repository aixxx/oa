<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttendanceWorkflowOvertimesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attendance_workflow_overtimes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('entry_id')->nullable()->unique('entry_id_unique')->comment('流程id');
			$table->integer('user_id')->nullable()->index('idx_user_id')->comment('发起人id');
			$table->string('title', 128)->comment('标题');
			$table->dateTime('begin_time')->comment('开始时间');
			$table->dateTime('end_time')->comment('结束时间');
			$table->float('time_sub_by_hour', 10, 0)->unsigned()->comment('加班时长');
			$table->timestamps();
			$table->dateTime('finished_at')->nullable()->index('idx_finished_at')->comment('审批完成时间');
			$table->text('note', 65535)->nullable()->comment('备注');
			$table->integer('file_upload')->default(0)->comment('上传附件');
			$table->string('ehr_deal_status', 191)->nullable()->comment('ehr数据处理状态');
			$table->index(['begin_time','end_time'], 'idx_date_begin_end');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('attendance_workflow_overtimes');
	}

}
