<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttendanceSheetsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attendance_sheets', function(Blueprint $table)
		{
			$table->increments('attendance_id')->comment('主键');
			$table->dateTime('attendance_date')->index('idx_date')->comment('日期');
			$table->integer('attendance_user_id')->nullable()->index('idx_user_id')->comment('员工编号');
			$table->integer('attendance_work_id')->nullable()->comment('班值类型');
			$table->dateTime('attendance_begin_at')->nullable()->comment('实际考勤上班时间');
			$table->dateTime('attendance_end_at')->nullable()->comment('实际考勤下班时间');
			$table->text('attendance_time', 65535)->nullable()->comment('打卡时间');
			$table->boolean('attendance_is_abnormal')->nullable()->comment('是否异常(0:否 1:是)');
			$table->string('attendance_abnormal_note', 200)->nullable()->comment('异常说明');
			$table->timestamp('attendance_create_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'))->comment('创建时间');
			$table->dateTime('attendance_update_at')->nullable()->comment('修改时间');
			$table->float('attendance_length', 10, 0)->nullable();
			$table->boolean('attendance_is_manual')->default(0)->comment('是否手动处理(0:自动;1.手动)');
			$table->string('attendance_holiday_type', 64)->default('')->comment('休假类型1');
			$table->float('attendance_holiday_type_sub')->default(0.00)->comment('休假时长1');
			$table->integer('attendance_holiday_entry_id')->default(0)->comment('流程编号1');
			$table->string('attendance_holiday_type_second', 64)->default('')->comment('休假类型2');
			$table->float('attendance_holiday_type_sub_second')->default(0.00)->comment('休假时长2');
			$table->integer('attendance_holiday_entry_id_second')->default(0)->comment('流程编号2');
			$table->float('attendance_travel_interval')->default(0.00)->comment('出差时长');
			$table->integer('attendance_travel_entry_id')->default(0)->comment('出差流程编号');
			$table->float('attendance_overtime_sub')->default(0.00)->comment('加班时长');
			$table->integer('attendance_overtime_entry_id')->default(0)->comment('加班流程编号');
			$table->index(['attendance_user_id','attendance_date']);
			$table->unique(['attendance_date','attendance_user_id'], 'unique_date_user_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('attendance_sheets');
	}

}
