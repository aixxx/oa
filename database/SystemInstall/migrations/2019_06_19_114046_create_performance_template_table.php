<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePerformanceTemplateTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('performance_template', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned()->comment('自动编号');
			$table->bigInteger('user_id')->unsigned()->nullable()->comment('用户编号');
			$table->bigInteger('company_id')->unsigned()->nullable()->comment('公司编号');
			$table->string('title', 100)->nullable()->comment('模板标题名称');
			$table->boolean('is_status')->nullable()->default(1)->comment('是否关联过结果 1是关联成功');
			$table->boolean('status')->nullable()->default(1)->comment('状态');
			$table->timestamps();
			$table->bigInteger('type_id')->unsigned()->nullable()->comment('员工类型');
			$table->bigInteger('department_id')->unsigned()->nullable()->comment('员工部门');
			$table->string('object', 50)->nullable()->comment('考核对象');
			$table->integer('review_time')->nullable()->default(0)->comment('自评时间');
			$table->integer('remind_time')->nullable()->default(0)->comment('自评提醒时间');
			$table->integer('money')->nullable()->default(0)->comment('绩效金额');
			$table->integer('number')->nullable()->default(0)->comment('总权重');
			$table->integer('usage_number')->nullable()->default(0)->comment('使用人数');
			$table->text('userarr', 65535)->nullable()->comment('广联绩效模板的用户id(11,22)');
			$table->integer('frequency')->nullable()->default(0)->comment('审核次数');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('performance_template');
	}

}
