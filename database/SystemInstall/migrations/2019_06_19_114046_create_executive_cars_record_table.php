<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateExecutiveCarsRecordTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('executive_cars_record', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('cars_id')->comment('车ID');
			$table->integer('type')->comment('记录类型,1-年检，2-保险，3-违章，4-事故，5-保养，6-维修，7-加油');
			$table->string('status', 20)->comment('车检状态');
			$table->date('dates')->comment('车检日期');
			$table->string('address', 50)->comment('车检地址');
			$table->string('append', 100)->nullable()->comment('车检附件');
			$table->timestamps();
			$table->softDeletes()->index()->comment('删除时间');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('executive_cars_record');
	}

}
