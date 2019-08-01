<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAddworkAuditPeoplesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('addwork_audit_peoples', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('addwork_id')->comment('加班申请id');
			$table->bigInteger('user_id')->comment('抄送人或者审批人的id');
			$table->string('user_name', 50)->comment('抄送人或者审批人的姓名');
			$table->boolean('hierarchy')->comment('审批人的层级');
			$table->boolean('type')->comment('1：审批人，2：抄送人');
			$table->timestamp('deleted_at')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('删除时间');
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
		Schema::drop('addwork_audit_peoples');
	}

}
