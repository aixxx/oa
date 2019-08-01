<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttendanceWorkflowTravelsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attendance_workflow_travels', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('entry_id')->nullable()->unique('entry_id_unique')->comment('流程id');
			$table->integer('user_id')->nullable()->index('idx_user_id')->comment('发起人id');
			$table->string('chinese_name', 64)->comment('中文姓名');
			$table->string('primary_dept', 128)->default('0')->index('idx_primary_dept')->comment('主部门');
			$table->string('title', 128)->comment('标题');
			$table->dateTime('date_begin')->comment('开始时间');
			$table->dateTime('date_end')->comment('结束时间');
			$table->float('date_interval', 10, 0)->unsigned()->comment('出差天数（单位：天）');
			$table->timestamps();
			$table->dateTime('finished_at')->nullable()->index('idx_finished_at')->comment('审批完成时间');
			$table->string('address', 128)->comment('出差地点');
			$table->text('cause', 65535)->nullable()->comment('出差事由');
			$table->text('note', 65535)->nullable()->comment('备注');
			$table->integer('file_upload')->default(0)->comment('上传附件');
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
		Schema::drop('attendance_workflow_travels');
	}

}
