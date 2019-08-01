<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVacationLeaveRecordTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('vacation_leave_record', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uid')->comment('用户id');
			$table->integer('company_id')->comment('公司id');
			$table->integer('entry_id')->comment('工作流申请id');
			$table->timestamp('begin_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('开始时间');
			$table->dateTime('end_time')->default('0000-00-00 00:00:00')->comment('结束时间');
			$table->text('reson', 65535)->comment('请假事由');
			$table->integer('time_sub_by_hour')->default(0)->comment('时长统计');
			$table->integer('vacation_type')->comment('请假类型');
			$table->integer('balance')->comment('假期剩余');
			$table->string('file_upload', 191)->comment('图片');
			$table->timestamps();
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('vacation_leave_record');
	}

}
