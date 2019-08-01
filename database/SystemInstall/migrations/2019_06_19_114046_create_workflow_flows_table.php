<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWorkflowFlowsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('workflow_flows', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('flow_no', 45);
			$table->string('flow_name', 45);
			$table->integer('template_id');
			$table->integer('type_id');
			$table->text('flowchart', 65535);
			$table->text('jsplumb', 65535);
			$table->boolean('is_publish');
			$table->boolean('is_show');
			$table->text('introduction', 65535)->comment('流程说明');
			$table->timestamps();
			$table->string('leader_link_type')->default('')->comment('领导人审批条线,business:业务条线;report:汇报关系条线');
			$table->string('icon_url')->nullable()->comment('icon 图片');
			$table->string('route_url', 100)->nullable()->comment('路由');
			$table->integer('version')->default(0)->comment('版本号，以时间戳做版本');
			$table->boolean('is_abandon')->default(0)->comment('是否废弃，1：废弃；0：未废弃');
			$table->text('can_view_users', 65535)->nullable()->comment('能看到本流程的用户id集合');
			$table->text('can_view_departments', 65535)->nullable()->comment('能看到本流程的部门id集合');
			$table->string('show_route_url', 100)->comment('前端展示路由');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('workflow_flows');
	}

}
