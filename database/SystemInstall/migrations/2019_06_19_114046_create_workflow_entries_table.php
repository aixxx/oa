<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWorkflowEntriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('workflow_entries', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('title', 45)->default('')->comment('标题');
			$table->integer('user_id');
			$table->integer('flow_id');
			$table->integer('process_id');
			$table->integer('circle');
			$table->integer('status')->comment('当前状态 0处理中 9通过 -1驳回 -2撤销 -9草稿\n1：流程中\n9：处理完成');
			$table->integer('pid');
			$table->integer('enter_process_id');
			$table->integer('enter_proc_id');
			$table->integer('child');
			$table->timestamps();
			$table->softDeletes();
			$table->integer('origin_auth_id')->default(0)->comment('申请单真实登录人id');
			$table->string('origin_auth_name', 128)->default('')->comment('申请单真实登录人姓名');
			$table->dateTime('finish_at')->nullable()->comment('流程审批完成时间');
			$table->string('order_no', 64)->nullable()->comment('审批单号');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('workflow_entries');
	}

}
