<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWorkflowProcessesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('workflow_processes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('flow_id');
			$table->string('process_name', 64);
			$table->integer('limit_time')->default(0)->comment('限定时间,单位秒');
			$table->string('type', 32)->default('operation')->comment('流程图显示操作框类型');
			$table->string('icon', 32)->default('')->comment('流程图显示图标');
			$table->string('process_to', 32)->default('');
			$table->text('style', 65535);
			$table->string('style_color', 128)->default('#78a300');
			$table->string('style_height', 191)->default('30');
			$table->string('style_width', 191)->default('30');
			$table->string('position_left', 191)->default('100px');
			$table->string('position_top', 191)->default('200px');
			$table->integer('position')->default(1)->comment('步骤位置');
			$table->integer('child_flow_id')->default(0)->comment('子流程id');
			$table->integer('child_after')->default(2)->comment('子流程结束后 1.同时结束父流程 2.返回父流程');
			$table->integer('child_back_process')->default(0)->comment('子流程结束后返回父流程进程');
			$table->string('description', 191)->default('')->comment('步骤描述');
			$table->timestamps();
			$table->boolean('can_merge')->default(1)->comment('是否可以做节点合并，1:可以;0:不可以');
			$table->string('pass_events', 256)->default('')->comment('节点审批通过后触发的事件，以逗号分割');
			$table->string('reject_events', 256)->default('')->comment('节点审批拒绝后触发的事件，以逗号分割');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('workflow_processes');
	}

}
