<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWorkflowRoleTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('workflow_role', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('role_name', 45)->comment('角色中文名');
			$table->timestamps();
			$table->softDeletes();
			$table->string('company_id')->default('0')->comment('负责的公司,以逗号间隔');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('workflow_role');
	}

}
