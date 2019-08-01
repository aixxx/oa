<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttendanceWorkUserLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attendance_work_user_logs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->dateTime('date')->index('Idx_date')->comment('时间');
			$table->string('class_title', 20)->nullable()->comment('排班代码');
			$table->integer('user_id')->nullable()->index('Idx_class_id_user_id')->comment('员工编号');
			$table->boolean('status')->default(1)->comment('状态(1:有效；2:无效)');
			$table->timestamp('create_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'))->comment('创建时间');
			$table->dateTime('updat_at')->nullable()->comment('修改时间');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('attendance_work_user_logs');
	}

}
