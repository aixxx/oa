<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttendanceApiClassesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attendance_api_classes', function(Blueprint $table)
		{
			$table->increments('id')->comment('自动编号');
			$table->string('title', 50)->comment('班次名称');
			$table->string('code', 10)->comment('班次编号，最多10个字符');
			$table->boolean('type')->comment('上下班类别 1-一次，2-两次，3-三次');
			$table->time('work_time_begin1')->nullable()->comment('上班时间1');
			$table->time('work_time_end1')->nullable()->comment('下班时间1');
			$table->time('work_time_begin2')->nullable()->comment('上班时间2');
			$table->time('work_time_end2')->nullable()->comment('下班时间2');
			$table->time('work_time_begin3')->nullable()->comment('上班时间3');
			$table->time('work_time_end3')->nullable()->comment('下班时间3');
			$table->boolean('is_siesta')->comment('是否开启午休 1-开启， 2-关闭');
			$table->time('begin_siesta_time')->nullable()->comment('午休开始时间');
			$table->time('end_siesta_time')->nullable()->comment('午休结束时间');
			$table->integer('clock_time_begin1')->nullable()->comment('允许上班打卡时间1');
			$table->integer('clock_time_end1')->nullable()->comment('允许下班打卡时间1');
			$table->integer('clock_time_begin2')->nullable()->comment('允许上班打卡时间2');
			$table->integer('clock_time_end2')->nullable()->comment('允许下班打卡时间2');
			$table->integer('clock_time_begin3')->nullable()->comment('允许上班打卡时间3');
			$table->integer('clock_time_end3')->nullable()->comment('允许下班打卡时间3');
			$table->integer('elastic_min')->nullable()->comment('弹性标准');
			$table->integer('serious_late_min')->nullable()->comment('严重迟到标准');
			$table->integer('absenteeism_min')->nullable()->comment('旷工标准');
			$table->integer('admin_id')->nullable()->comment('操作人员ID');
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
		Schema::drop('attendance_api_classes');
	}

}
