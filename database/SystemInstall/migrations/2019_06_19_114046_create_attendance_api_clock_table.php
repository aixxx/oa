<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttendanceApiClockTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attendance_api_clock', function(Blueprint $table)
		{
			$table->increments('id')->comment('自动编号');
			$table->integer('user_id')->comment('用户ID');
			$table->date('dates')->comment('打卡日期');
			$table->dateTime('datetimes')->comment('打卡时间');
			$table->string('remark')->nullable()->comment('备注');
			$table->string('remark_image')->nullable()->comment('备注图片');
			$table->boolean('type')->comment('打卡类型 1-上班 2-下班');
			$table->string('clock_address_type')->nullable()->comment('打卡地址类型， 1- 公司内打卡 2-外勤打卡 3-出差打卡');
			$table->timestamps();
			$table->softDeletes()->index()->comment('删除时间');
			$table->boolean('clock_nums')->default(1);
			$table->integer('classes_id')->default(0)->comment('班次ID');
			$table->boolean('status')->default(1)->comment('状态。1-正常，0-无效');
			$table->string('clock_address')->nullable()->comment('打卡地址');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('attendance_api_clock');
	}

}
