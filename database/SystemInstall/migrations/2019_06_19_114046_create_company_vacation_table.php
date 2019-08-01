<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCompanyVacationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('company_vacation', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('company_id')->comment('公司id');
			$table->string('vacation_name', 20)->comment('假期名');
			$table->integer('cost_unit_type')->nullable()->default(1)->comment('最小请假单位1、按1小时2、按半天3、按一天4、一次请完');
			$table->integer('leave_type')->nullable()->default(1)->comment('请假时长方式1、按工作日计算请假时长2、按自然日计算请假时长');
			$table->boolean('is_balance')->nullable()->default(1)->comment('是否启用余额1、开0、关');
			$table->integer('balance_type')->nullable()->default(1)->comment('余额发放形式1、每年自动固定发放天数2、按照入职时间自动发放3、加班时长自动计入余额');
			$table->integer('per_count')->nullable()->default(0)->comment('每人发放天数');
			$table->integer('expire_time')->nullable()->default(0)->comment('规则有效期1、按自然年(1月1日 - 12月31日)2、按入职日期12月');
			$table->boolean('is_add_expire')->nullable()->default(0)->comment('是否支持延长有效期1、是0、否');
			$table->integer('add_time')->nullable()->default(0)->comment('可以延长的天数');
			$table->integer('leave_start_type')->nullable()->default(0)->comment('新员工何时可以请假1、入职当天2、转正');
			$table->softDeletes();
			$table->timestamps();
			$table->integer('discount_salary')->nullable()->default(100)->comment('工资折扣%');
			$table->index(['company_id','vacation_name']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('company_vacation');
	}

}
