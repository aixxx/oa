<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttendanceWorkflowLeavesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attendance_workflow_leaves', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('entry_id')->nullable()->unique('entry_id_unique')->comment('流程id');
			$table->integer('user_id')->nullable()->index('idx_user_id')->comment('发起人id');
			$table->string('title', 128)->comment('标题');
			$table->string('holiday_type', 64)->default('')->index('idx_holiday_type')->comment('休假类型');
			$table->dateTime('date_begin')->comment('开始时间');
			$table->dateTime('date_end')->comment('结束时间');
			$table->float('date_time_sub', 10, 0)->unsigned()->comment('请假时长');
			$table->timestamps();
			$table->dateTime('finished_at')->nullable()->index('idx_finished_at')->comment('审批完成时间');
			$table->text('remark', 65535)->nullable()->comment('备注');
			$table->integer('file_upload')->nullable()->default(0)->comment('上传附件');
			$table->string('ehr_deal_status', 191)->nullable()->comment('ehr数据处理状态');
			$table->boolean('is_resumed')->default(0)->comment('休假1是否销假:(0:未销;1:已销)');
			$table->index(['date_begin','date_end'], 'idx_date_begin_end');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('attendance_workflow_leaves');
	}

}
