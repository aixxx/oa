<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSalaryFormTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('salary_form', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('user_id')->unsigned();
			$table->bigInteger('employee_num')->unsigned();
			$table->integer('year');
			$table->integer('month');
			$table->decimal('base', 10)->default(0.00)->comment('基础薪资总额');
			$table->string('base_json', 191)->nullable()->comment('基础薪资组成');
			$table->decimal('subsidy', 10)->default(0.00)->comment('补贴总额');
			$table->string('subsidy_json', 191)->default('0')->comment('补贴组成');
			$table->decimal('bonus', 10)->default(0.00)->comment('奖励金额');
			$table->decimal('fines', 10)->default(0.00)->comment('惩罚金额');
			$table->decimal('dividend', 10)->default(0.00)->comment('分红金额');
			$table->decimal('should_salary', 10)->default(0.00)->comment('应发薪资');
			$table->decimal('actual_salary', 10)->default(0.00)->comment('实发薪资');
			$table->decimal('float_salary', 10)->default(0.00)->comment('浮动薪资');
			$table->string('remark', 191)->nullable()->comment('备注');
			$table->boolean('is_send')->default(0);
			$table->boolean('is_pass')->default(0);
			$table->string('auditor_note', 191)->nullable()->comment('审核意见');
			$table->integer('entry_id')->nullable()->comment('流程申请ID');
			$table->softDeletes();
			$table->timestamps();
			$table->decimal('human_cost', 10)->default(0.00)->comment('个人人力成本');
			$table->boolean('is_view')->default(0)->comment('是否查看过,0:未查看,1:已查看');
			$table->boolean('is_confirm')->default(0)->comment('是否确认,0:未确认,1:已确认');
			$table->boolean('is_withdraw')->default(0)->comment('是否撤销,0:未撤销,1:已撤销');
			$table->decimal('performance', 10)->default(0.00)->comment('个人绩效奖金');
			$table->integer('attendance_id')->nullable()->comment('关联的考勤ID');
			$table->string('greetings', 64)->nullable()->comment('问候语');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('salary_form');
	}

}
