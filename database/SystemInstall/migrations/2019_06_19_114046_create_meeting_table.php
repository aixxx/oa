<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMeetingTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('meeting', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned()->comment('自动编号');
			$table->bigInteger('mr_id')->unsigned()->nullable()->comment('会议室id');
			$table->string('title', 100)->nullable()->comment('会议名称');
			$table->text('describe', 65535)->nullable()->comment('会议描述');
			$table->bigInteger('user_id')->unsigned()->nullable()->comment('会议创建人id');
			$table->bigInteger('host_id')->unsigned()->nullable()->comment('主持人id');
			$table->dateTime('start')->nullable()->comment('开始时间');
			$table->dateTime('end')->nullable()->comment('结束时间');
			$table->string('day', 20)->nullable()->comment('年月日');
			$table->string('remind', 100)->nullable()->comment('提示设置');
			$table->text('meeting_file', 65535)->nullable()->comment('会议文件');
			$table->text('meeting_summary', 65535)->nullable()->comment('会议纪要');
			$table->integer('number')->nullable()->default(0)->comment('参与人数');
			$table->boolean('repeat_type')->nullable()->default(1)->comment('重复状态 0重复 1不重复');
			$table->boolean('send_type')->nullable()->default(1)->comment('发送方式 0应用 1短信');
			$table->boolean('status')->nullable()->default(0)->comment('状态 0已取消 1可使用');
			$table->timestamps();
			$table->string('duration', 20)->nullable()->comment('会议时长');
			$table->string('code', 25)->nullable()->comment('会议编号');
			$table->string('position', 100)->nullable()->comment('会议位置');
			$table->bigInteger('entrise_id')->unsigned()->nullable()->comment('会议工作流编号');
			$table->dateTime('deadline')->nullable()->comment('提醒截止时间');
			$table->integer('repetition_time')->nullable()->default(0)->comment('重复时间');
			$table->integer('frequency')->nullable()->default(0)->comment('提醒次数');
			$table->bigInteger('summary_id')->unsigned()->nullable()->comment('会议记录工作流编号id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('meeting');
	}

}
