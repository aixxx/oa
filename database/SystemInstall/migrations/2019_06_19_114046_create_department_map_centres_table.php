<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDepartmentMapCentresTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('department_map_centres', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->comment('操作员工ID');
			$table->string('department_level1', 191)->comment('一级部门');
			$table->string('department_level2', 191)->comment('二级部门');
			$table->string('department_full_path', 191)->comment('部门全路径');
			$table->string('centre_name', 191)->comment('部门对应的中心名称');
			$table->integer('times')->comment('次数');
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
		Schema::drop('department_map_centres');
	}

}
