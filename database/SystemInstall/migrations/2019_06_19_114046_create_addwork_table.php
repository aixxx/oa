<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAddworkTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('addwork', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->bigInteger('user_id')->comment('申请加班的人员id');
			$table->string('name', 50)->comment('申请人名字');
			$table->integer('company_id')->comment('公司id');
			$table->integer('department_id')->comment('部门id');
			$table->string('position')->comment('职位');
			$table->string('numbers')->comment('审批编号');
			$table->dateTime('add_time')->comment('申请时间');
			$table->dateTime('begin_time')->comment('开始时间');
			$table->dateTime('end_time')->comment('结束时间');
			$table->integer('duration')->comment('时长');
			$table->string('cause')->nullable()->comment('加班原因');
			$table->integer('status')->default(2)->comment('申请状态，默认为2：未审批完，3：已同意，4：已拒绝，5：已撤销');
			$table->string('revocation_cause')->nullable()->comment('撤销原因');
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
		Schema::drop('addwork');
	}

}
