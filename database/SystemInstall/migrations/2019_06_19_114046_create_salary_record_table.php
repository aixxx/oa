<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSalaryRecordTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('salary_record', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('year');
			$table->integer('month');
			$table->integer('count')->comment('当月总发薪人数');
			$table->decimal('total_amount', 15)->comment('当月薪资总额');
			$table->decimal('should_amount', 15)->comment('当月应发薪资总额');
			$table->decimal('actual_amount', 15)->comment('当月实发薪资总额');
			$table->decimal('performance_amount', 15)->comment('当月绩效奖金总额');
			$table->decimal('bonus_amount', 15)->comment('当月奖励金金总额');
			$table->decimal('fines_amount', 15)->comment('当月惩罚金总额');
			$table->decimal('overtime_salary_amount', 15)->comment('当月加班工资总额');
			$table->decimal('single_salary_amount', 15)->comment('当月固定工资总额');
			$table->decimal('social_company_amount', 15)->comment('当月企业社保总额');
			$table->decimal('fund_company_amount', 15)->comment('当月企业公积金总额');
			$table->decimal('float_salary_amount', 15)->comment('当月浮动薪资总额');
			$table->boolean('status')->default(0)->comment('当月薪资计算状态,0:进行中,1:计算完成');
			$table->string('remark', 191)->nullable()->comment('备注');
			$table->softDeletes();
			$table->timestamps();
			$table->integer('entry_id')->nullable()->default(0)->comment('流程申请ID');
			$table->integer('status_entry')->nullable()->default(0)->comment('薪资统计申请是否通过,0:进行中,1:通过');
			$table->string('title', 64)->nullable()->comment('标题');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('salary_record');
	}

}
