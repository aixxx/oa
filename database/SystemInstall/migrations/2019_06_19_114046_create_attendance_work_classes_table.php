<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttendanceWorkClassesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attendance_work_classes', function(Blueprint $table)
		{
			$table->increments('class_id');
			$table->string('class_title', 20)->default('')->index('Idx_class_title')->comment('班值代码');
			$table->string('class_name', 64)->comment('班值名称');
			$table->string('class_begin_at', 32)->nullable()->comment('上班时间');
			$table->string('class_end_at', 32)->nullable()->comment('下班时间');
			$table->string('class_rest_begin_at', 32)->nullable()->comment('休息开始时间');
			$table->string('class_rest_end_at', 32)->nullable()->comment('休息结束时间');
			$table->boolean('class_times')->comment('一日几次班值');
			$table->integer('class_create_user_id')->comment('创建人');
			$table->integer('class_update_user_id')->comment('修改人');
			$table->timestamp('class_create_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'))->comment('创建时间');
			$table->dateTime('class_update_at')->nullable()->comment('修改时间');
			$table->boolean('type')->comment('所属类型(1.客服类;2.职能类;3.弹性类)');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('attendance_work_classes');
	}

}
