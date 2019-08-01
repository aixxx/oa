<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMeetingRoomTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('meeting_room', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned()->comment('自动编号');
			$table->string('title', 100)->nullable()->comment('会议室名称');
			$table->string('code', 20)->nullable()->comment('会议室编号');
			$table->bigInteger('user_id')->unsigned()->nullable()->comment('会议室添加人id');
			$table->string('position', 100)->nullable()->comment('会议室位置');
			$table->string('configure', 100)->nullable()->comment('设备配置（白板,会议桌椅,投影仪）');
			$table->string('number', 10)->nullable()->comment('会议人数');
			$table->char('start', 6)->nullable()->comment('开始预约');
			$table->char('end', 6)->nullable()->comment('截止预约时间');
			$table->text('remarks', 65535)->nullable()->comment('备注');
			$table->boolean('status')->nullable()->default(0)->comment('0已删除  1可使用 2已停用 ');
			$table->timestamps();
			$table->integer('entrise_id')->nullable()->default(0)->comment('工作流id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('meeting_room');
	}

}
