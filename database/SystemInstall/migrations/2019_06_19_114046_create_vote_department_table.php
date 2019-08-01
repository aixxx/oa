<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVoteDepartmentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('vote_department', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('company_id')->comment('公司编号');
			$table->integer('department_id')->comment('部门编号');
			$table->integer('v_id')->comment('投票编号');
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
		Schema::drop('vote_department');
	}

}
