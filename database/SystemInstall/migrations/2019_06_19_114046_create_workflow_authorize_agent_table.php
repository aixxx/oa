<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWorkflowAuthorizeAgentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('workflow_authorize_agent', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('authorizer_user_id')->comment('授权人id');
			$table->string('authorizer_user_name', 128)->comment('授权人姓名');
			$table->dateTime('authorize_valid_begin')->nullable()->comment('代理权限开始时间');
			$table->dateTime('authorize_valid_end')->nullable()->comment('代理权限结束时间');
			$table->string('flow_no', 45)->default('0')->comment('代理审批的流程');
			$table->integer('agent_user_id')->comment('代理人id');
			$table->string('agent_user_name', 128)->comment('代理人姓名');
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
		Schema::drop('workflow_authorize_agent');
	}

}
