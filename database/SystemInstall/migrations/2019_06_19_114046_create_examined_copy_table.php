<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateExaminedCopyTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('examined_copy', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('l_id')->comment('假期id');
			$table->integer('type')->comment('类型 1：审批人 2：抄送人');
			$table->integer('user_id')->comment('审批人或抄送人id');
			$table->integer('step')->comment('层级');
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
		Schema::drop('examined_copy');
	}

}
