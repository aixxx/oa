<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAbilitiesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('abilities', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 150)->unique();
			$table->string('title', 191)->nullable();
			$table->integer('entity_id')->unsigned()->nullable();
			$table->string('entity_type', 150)->nullable();
			$table->boolean('only_owned')->default(0);
			$table->integer('scope')->nullable()->index();
			$table->timestamps();
			$table->integer('level1_no')->comment('一级菜单');
			$table->integer('level2_no')->comment('二级菜单');
			$table->integer('level3_no')->comment('三级菜单');
			$table->string('root_code', 64)->comment('一级菜单代码');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('abilities');
	}

}
