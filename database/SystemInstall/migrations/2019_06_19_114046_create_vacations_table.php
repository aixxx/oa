<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVacationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('vacations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->comment('用户id');
			$table->integer('ndays')->comment('剩余年假时长');
			$table->integer('txdays')->comment('剩余调休时长');
			$table->integer('cdays')->comment('剩余产假时长');
			$table->integer('pcdays')->comment('剩余陪产假时长');
			$table->integer('hdays')->comment('剩余婚假时长');
			$table->integer('ldays')->comment('剩余例假时长');
			$table->integer('sdays')->comment('剩余丧假时长');
			$table->integer('brdays')->comment('剩余哺乳假时长');
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
		Schema::drop('vacations');
	}

}
