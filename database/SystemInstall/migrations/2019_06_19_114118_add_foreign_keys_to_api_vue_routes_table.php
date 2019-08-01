<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToApiVueRoutesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('api_vue_routes', function(Blueprint $table)
		{
			$table->foreign('route_id')->references('id')->on('api_routes')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('action_id')->references('id')->on('api_vue_action')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('api_vue_routes', function(Blueprint $table)
		{
			$table->dropForeign('api_vue_routes_route_id_foreign');
			$table->dropForeign('api_vue_routes_action_id_foreign');
		});
	}

}
