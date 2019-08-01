<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateApiPositionsRolesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('api_positions_roles', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('position_id')->comment('职位ID');
			$table->integer('role_id')->comment('角色ID');
			$table->string('title', 191)->nullable()->comment('名称');
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
		Schema::drop('api_positions_roles');
	}

}