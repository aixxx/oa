<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateReportRulesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('report_rules', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('user_id')->unsigned()->default(0)->index('user_id')->comment('创建者id');
			$table->integer('company_id')->default(0)->comment('创建者所属公司id');
			$table->boolean('report_type')->default(0)->index('template_id')->comment('汇报类型，与汇报模板关联');
			$table->boolean('send_cycle')->default(1)->index('cycle')->comment('1每天2每周3每月');
			$table->string('send_day_date', 50)->nullable()->default('')->comment('日报周期值（星期一-星期天）');
			$table->string('stime', 50)->default('0')->comment('开始时间');
			$table->string('etime', 50)->default('0')->comment('截止时间');
			$table->boolean('is_legal_day_send')->default(0)->comment('法定假日 0不提交1提交');
			$table->boolean('is_remind')->default(0)->comment('0不提醒1提醒');
			$table->boolean('remind_time')->default(1)->comment('提醒时间');
			$table->string('remind_content')->nullable()->comment('提醒内容');
			$table->text('select_user', 65535)->nullable()->comment('选择的员工');
			$table->text('select_department', 65535)->nullable()->comment('选择的部门');
			$table->dateTime('deleted_at')->default('0000-00-00 00:00:00');
			$table->timestamps();
			$table->string('send_week_date', 50)->nullable()->default('')->comment('周报周期值（星期五-星期一）');
			$table->string('send_month_date', 50)->nullable()->default('')->comment('月报周期值（15号-5号）');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('report_rules');
	}

}
