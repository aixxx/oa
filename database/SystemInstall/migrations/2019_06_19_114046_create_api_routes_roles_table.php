<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateApiRoutesRolesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('api_routes_roles', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('title', 191)->nullable()->comment('名称');
			$table->integer('action_id')->comment('前台ID');
			$table->integer('role_id')->comment('角色ID');
			$table->softDeletes();
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
		Schema::drop('api_routes_roles');
	}

}
