<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTaskTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('task', function(Blueprint $table)
		{
			$table->increments('id')->comment('自动编号');
			$table->text('info', 65535)->comment('任务详情');
			$table->char('enclosure', 191)->nullable()->comment('任务附件');
			$table->boolean('send_type')->nullable()->comment('发送类型：1应用通知，2短信');
			$table->dateTime('deadline')->nullable()->comment('截止时间');
			$table->integer('remind_time')->nullable()->default(0)->comment('提醒时间:0:不提醒。1:15i。2:1h。3:3h。4:1d');
			$table->integer('create_user_id')->nullable()->comment('创建人id');
			$table->dateTime('send_time')->nullable()->comment('发送时间');
			$table->softDeletes();
			$table->timestamps();
			$table->string('enclosure_img')->nullable()->comment('附件图片');
			$table->dateTime('start_time')->nullable()->comment('任务开始时间');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('task');
	}

}
