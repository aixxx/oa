<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttendanceApiTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attendance_api', function(Blueprint $table)
		{
			$table->increments('id')->comment('自动编号');
			$table->string('title', 50)->comment('考勤组名称');
			$table->boolean('system_type')->default(1)->comment('考勤制度类型 1-固定值 2-排班制 3-自由制');
			$table->string('classes_id', 50)->nullable()->comment('班次ID');
			$table->string('weeks', 50)->nullable()->comment('考勤日期');
			$table->integer('cycle_id')->nullable()->comment('班次ID');
			$table->time('clock_node')->nullable()->comment('打卡节点');
			$table->integer('add_clock_num')->nullable()->default(0)->comment('班次ID');
			$table->string('address', 100)->nullable()->comment('上班地址');
			$table->integer('clock_range')->nullable()->comment('允许打卡范围');
			$table->string('wifi_title', 100)->nullable()->comment('办公wifi名称');
			$table->integer('head_user_id')->nullable()->comment('考勤组负责人');
			$table->integer('overtime_rule_id')->nullable()->comment('加班规则ID');
			$table->boolean('is_getout_clock')->default(1)->comment('是否允许外勤打卡， 1-允许 0-不允许');
			$table->timestamps();
			$table->softDeletes()->index()->comment('删除时间');
			$table->integer('admin_id')->comment('系统最后操作人员ID');
			$table->string('lng', 20)->comment('经度');
			$table->string('lat', 20)->comment('纬度');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('attendance_api');
	}

}
