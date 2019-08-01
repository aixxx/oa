<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateApiRoutesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('api_routes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('path', 191)->nullable()->comment('路由');
			$table->string('title', 191)->nullable()->comment('名称');
			$table->integer('parent_id')->comment('父ID');
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
		Schema::drop('api_routes');
	}

}
