<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMeetingParticipantTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('meeting_participant', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned()->comment('自动编号');
			$table->bigInteger('user_id')->unsigned()->nullable()->comment('用户编号');
			$table->bigInteger('m_id')->unsigned()->nullable()->comment('会议编号id');
			$table->boolean('signin')->nullable()->default(0)->comment('类型 0未签到 1已签到');
			$table->boolean('type')->nullable()->default(0)->comment('类型 0参与人 1抄送人');
			$table->boolean('status')->nullable()->default(1)->comment('状态 0删除 1未查看  2已查看');
			$table->timestamps();
			$table->string('chinese_name', 50)->nullable()->comment('用户名');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('meeting_participant');
	}

}
