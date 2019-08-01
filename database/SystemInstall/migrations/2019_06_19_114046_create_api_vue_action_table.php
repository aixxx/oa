<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateApiVueActionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('api_vue_action', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('vue_path', 191)->nullable()->unique()->comment('前台路由');
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
		Schema::drop('api_vue_action');
	}

}
