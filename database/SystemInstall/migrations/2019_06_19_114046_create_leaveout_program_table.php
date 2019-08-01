<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeaveoutProgramTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('leaveout_program', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->comment('申请人员id');
			$table->integer('leaveout_id')->comment('外出申请id');
			$table->string('shenpi_no', 191)->comment('审批单号');
			$table->timestamp('add_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('创建时间');
			$table->boolean('now_node')->comment('当前节点id');
			$table->boolean('status')->comment('审批状态:0 未审批完成 1已批准 -1已拒绝 3已撤销');
			$table->boolean('is_edit')->comment('1修改中');
			$table->string('check_user_id')->comment('审批人');
			$table->string('copy_user_id')->comment('抄送人id');
			$table->string('check_profile_id')->comment('审批职位id');
			$table->string('check_department_id')->comment('审批部门id');
			$table->softDeletes()->comment('0:正常 1：删除');
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
		Schema::drop('leaveout_program');
	}

}
