<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePositionDepartmentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('position_department', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('position_id', 191)->index()->comment('职位ID');
			$table->string('department_id', 191)->index()->comment('部门ID');
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
		Schema::drop('position_department');
	}

}
