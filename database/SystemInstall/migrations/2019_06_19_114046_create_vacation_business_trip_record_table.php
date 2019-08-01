<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVacationBusinessTripRecordTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('vacation_business_trip_record', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uid')->comment('用户id');
			$table->integer('company_id')->comment('公司id');
			$table->integer('entry_id')->comment('工作流申请id');
			$table->text('reson', 65535)->comment('出差事由');
			$table->integer('trip_days')->comment('出差天数');
			$table->text('remark', 65535)->nullable()->comment('出差备注');
			$table->string('other_people', 200)->nullable()->comment('同行人');
			$table->softDeletes();
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
		Schema::drop('vacation_business_trip_record');
	}

}
