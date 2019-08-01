<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTripUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('trip_user', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('tid')->comment('出差id');
			$table->integer('uid')->comment('用户id');
			$table->string('type_name')->comment('出差城市');
			$table->boolean('user_type')->comment('用户类型 （1.审批人  2.抄送人）');
			$table->integer('level')->default(0)->comment('审批步数 (当前是第几步,抄送人是0)');
			$table->integer('create_user_id')->comment('创建者id');
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
		Schema::drop('trip_user');
	}

}
