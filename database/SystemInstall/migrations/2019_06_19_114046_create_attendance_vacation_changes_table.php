<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttendanceVacationChangesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attendance_vacation_changes', function(Blueprint $table)
		{
			$table->increments('change_id');
			$table->integer('change_user_id')->nullable()->index()->comment('被修改人id');
			$table->string('change_type', 32)->default('')->comment('调整假日类型 1:法定年假 2：公司福利年假 3：全薪假 4：调休');
			$table->string('change_before_amount', 32)->default('0')->comment('调整前假期余额');
			$table->string('change_after_amount', 32)->default('0')->comment('调整后假期余额');
			$table->string('change_amount', 32)->default('0')->comment('调整的数量');
			$table->string('change_remark', 32)->default('')->comment('调整原因备注');
			$table->string('change_unit', 20)->default('hour')->comment('节假日结算单位(hour/day)');
			$table->string('change_entry_id', 128)->default('')->comment('流程编号');
			$table->timestamps();
			$table->string('update_user_id', 191)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('attendance_vacation_changes');
	}

}
