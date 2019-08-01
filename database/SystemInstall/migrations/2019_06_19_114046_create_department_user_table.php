<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDepartmentUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('department_user', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('department_id')->index();
			$table->integer('user_id')->index();
			$table->boolean('is_leader')->default(0);
			$table->boolean('is_primary')->nullable()->default(0);
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
		Schema::drop('department_user');
	}

}
