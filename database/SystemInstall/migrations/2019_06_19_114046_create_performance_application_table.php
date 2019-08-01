<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePerformanceApplicationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('performance_application', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned()->comment('自动编号');
			$table->string('title', 50)->nullable()->comment('绩效申请名称（2018年9月绩效考核申请）');
			$table->bigInteger('user_id')->unsigned()->nullable()->index()->comment('申请人编号');
			$table->bigInteger('pt_id')->unsigned()->nullable()->index()->comment('绩效模板数据ID');
			$table->integer('result')->nullable()->comment('审核结果（80%）');
			$table->boolean('status')->nullable()->default(0)->comment('0未评价 1执行中（审核）  2待确定  3申诉待处理 4申诉处理中 5申诉处理完成  6完成，7 被驳回');
			$table->timestamps();
			$table->string('number', 50)->comment('随机编号');
			$table->string('amonth', 100)->nullable()->comment('考评月份');
			$table->boolean('is_status')->nullable()->default(0)->comment('0表示未查看，1查看过');
			$table->string('view_password', 200)->nullable()->comment('查看密码');
			$table->integer('audit_times')->nullable()->default(0)->comment('审核次数');
			$table->integer('money')->nullable()->default(0)->comment('绩效金额');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('performance_application');
	}

}
