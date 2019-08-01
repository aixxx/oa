<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateApiVueRoutesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('api_vue_routes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('route_id')->unsigned()->index('api_vue_routes_route_id_foreign');
			$table->integer('action_id')->unsigned()->index('api_vue_routes_action_id_foreign');
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
		Schema::drop('api_vue_routes');
	}

}
