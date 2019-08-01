<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWorkflowTaskTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('workflow_task', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('task_key', 45)->unique('unique_idx_task_key')->comment('任务唯一编码');
			$table->text('request', 65535)->nullable()->comment('任务数据，Json格式');
			$table->text('response', 65535)->comment('任务执行结果');
			$table->integer('entry_id')->unsigned()->comment('工作流单号');
			$table->integer('proc_id')->unsigned()->comment('审批流程节点id');
			$table->integer('task_status')->comment('当前状态,0:处理中;1:处理成功;2:处理失败;3:新建');
			$table->integer('exec_times')->default(0)->comment('任务执行的次数');
			$table->string('queue_name', 128)->comment('推送的队列名');
			$table->timestamps();
			$table->string('call_back_res', 1000)->default('')->comment('回调数据记录');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('workflow_task');
	}

}
