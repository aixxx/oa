<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWorkflowProcsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('workflow_procs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('entry_id');
			$table->integer('flow_id')->comment('流程id');
			$table->integer('process_id');
			$table->string('process_name', 64);
			$table->integer('user_id');
			$table->string('user_name', 128)->comment('审核人名称');
			$table->string('dept_name', 191)->comment('审核人部门名称');
			$table->integer('auditor_id')->default(0)->comment('具体操作人');
			$table->string('auditor_name', 128)->default('')->comment('操作人名称');
			$table->string('auditor_dept', 191)->default('')->comment('操作人部门');
			$table->integer('status')->comment('当前处理状态 0待处理 9通过 -1驳回\n0：处理中\n-1：驳回\n9：会签');
			$table->string('content')->comment('批复内容');
			$table->boolean('is_read')->comment('是否查看');
			$table->boolean('is_real')->comment('审核人和操作人是否同一人');
			$table->integer('circle')->default(1);
			$table->text('beizhu', 65535);
			$table->integer('concurrence')->default(0)->comment('并行查找解决字段， 部门 角色 指定 分组用');
			$table->string('note', 191);
			$table->timestamps();
			$table->string('authorizer_ids', 64)->default('')->comment('授权人id，可能是多个,逗号分隔');
			$table->string('authorizer_names', 128)->default('')->comment('授权人姓名，可能是多个,逗号分隔');
			$table->integer('origin_auth_id')->default(0)->comment('审批的真实登录用户');
			$table->string('origin_auth_name', 128)->default('')->comment('审批的真实登录用户姓名');
			$table->dateTime('finish_at')->nullable()->comment('节点审批完成时间');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('workflow_procs');
	}

}
