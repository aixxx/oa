<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeaveoutTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('leaveout', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->comment('申请人员id');
			$table->integer('department_id')->comment('部门id');
			$table->integer('company_id')->comment('公司id');
			$table->string('position', 191)->comment('申请人职位');
			$table->string('reason')->comment('外出事由');
			$table->timestamp('add_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('申请时间');
			$table->dateTime('begin_time')->default('0000-00-00 00:00:00')->comment('外出开始时间');
			$table->dateTime('end_time')->default('0000-00-00 00:00:00')->comment('外出结束时间');
			$table->string('explain')->comment('说明');
			$table->string('address')->comment('外出地址');
			$table->boolean('duration')->comment('外出时长');
			$table->string('revoke_reason')->comment('撤销原因');
			$table->boolean('status')->comment('状态:默认0未审批 1已通过 2已拒绝 3已撤销');
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
		Schema::drop('leaveout');
	}

}
