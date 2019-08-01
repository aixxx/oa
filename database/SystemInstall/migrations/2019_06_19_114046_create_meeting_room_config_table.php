<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMeetingRoomConfigTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('meeting_room_config', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned()->comment('自动编号');
			$table->bigInteger('mr_id')->unsigned()->nullable()->comment('会议室id');
			$table->bigInteger('config_id')->unsigned()->nullable()->comment('会议室id');
			$table->boolean('status')->nullable()->default(0)->comment('0已删除  1可使用');
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('meeting_room_config');
	}

}
