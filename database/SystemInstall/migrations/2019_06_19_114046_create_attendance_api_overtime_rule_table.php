<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttendanceApiOvertimeRuleTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attendance_api_overtime_rule', function(Blueprint $table)
		{
			$table->increments('id')->comment('自动编号');
			$table->boolean('is_working_overtime')->default(1)->comment('工作日允许加班 1-允许 0-不允许');
			$table->boolean('working_overtime_type')->default(1)->comment('工作日加班计算方式， 1-需审批，以审批单为准。2-需审批，以打卡为准，但不能超过审批时长。3-无需审批，根据打卡时间为准');
			$table->integer('working_begin_time')->nullable()->default(30)->comment('工作日加班起算时间');
			$table->integer('working_min_overtime')->nullable()->default(60)->comment('工作日最小加班时长');
			$table->boolean('is_rest_overtime')->default(1)->comment('休息日允许加班 1-允许 0-不允许');
			$table->boolean('rest_overtime_type')->default(1)->comment('工作日加班计算方式， 1-需审批，以审批单为准。2-需审批，以打卡为准，但不能超过审批时长。3-无需审批，根据打卡时间为准');
			$table->integer('rest_min_overtime')->nullable()->default(60)->comment('休息日最小加班时长');
			$table->timestamps();
			$table->softDeletes()->index()->comment('删除时间');
			$table->string('title', 100)->comment('标题');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('attendance_api_overtime_rule');
	}

}
