<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToUserSchedulesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_schedules', function(Blueprint $table)
		{
			$table->foreign('schedule_id')->references('id')->on('schedules')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_schedules', function(Blueprint $table)
		{
			$table->dropForeign('user_schedules_schedule_id_foreign');
		});
	}

}
