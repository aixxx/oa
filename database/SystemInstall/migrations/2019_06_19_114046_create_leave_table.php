<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeaveTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('leave', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->comment('用户id');
			$table->string('anum', 50)->comment('审批编号');
			$table->string('sqtime', 50)->comment('申请时间');
			$table->integer('bm_id')->comment('部门id');
			$table->string('job', 50)->comment('职位');
			$table->string('stime', 50)->comment('假期开始时间');
			$table->string('etime', 50)->comment('假期截止时间');
			$table->integer('times')->comment('请假时长');
			$table->integer('v_id')->comment('请假类型');
			$table->string('message')->comment('请假事由');
			$table->string('image')->comment('附件图片');
			$table->integer('status')->comment('请假状态 0：审批中 1：审批通过 2：审批拒绝 3：撤销成功');
			$table->string('leavenum', 100)->comment('请假单号');
			$table->string('phone', 50)->comment('联系电话');
			$table->integer('tel')->comment('手机号');
			$table->string('address')->comment('联系地址');
			$table->integer('c_id')->comment('公司id');
			$table->string('reason')->comment('撤销的理由');
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
		Schema::drop('leave');
	}

}
