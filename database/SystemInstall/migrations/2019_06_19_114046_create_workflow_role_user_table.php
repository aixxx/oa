<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWorkflowRoleUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('workflow_role_user', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('creater_user_id')->default(0)->comment('创建人id');
			$table->string('creater_user_chinese_name', 100)->default('')->comment('创建人用户名');
			$table->integer('role_id')->unsigned()->comment('角色id');
			$table->integer('user_id')->unsigned()->comment('用户id');
			$table->string('user_chinese_name')->comment('用户中文名');
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
		Schema::drop('workflow_role_user');
	}

}
