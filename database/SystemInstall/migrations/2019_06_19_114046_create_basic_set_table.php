<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBasicSetTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('basic_set', function(Blueprint $table)
		{
			$table->increments('id')->comment('自增主键');
			$table->string('website_name')->comment('后台系统名称');
			$table->string('login_greetings')->comment('登录页欢迎词');
			$table->timestamps();
			$table->softDeletes()->comment('删除时间');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('basic_set');
	}

}
