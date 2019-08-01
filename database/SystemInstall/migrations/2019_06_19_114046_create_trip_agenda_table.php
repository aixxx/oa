<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTripAgendaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('trip_agenda', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('trip_id')->index()->comment('出差id');
			$table->boolean('vehicle')->comment('交通工具 (1.飞机 2.火车 3.汽车 4.其他)');
			$table->boolean('go_type')->comment('行程类型 （1.单程  2.往返）');
			$table->string('depart_city', 191)->comment('出差城市');
			$table->string('whither_city', 191)->comment('目的城市');
			$table->dateTime('begin_time')->nullable()->comment('开始时间');
			$table->dateTime('end_time')->nullable()->comment('结束时间');
			$table->boolean('time_count')->comment('时长统计（天）');
			$table->timestamps();
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('trip_agenda');
	}

}
