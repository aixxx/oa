<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTripTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('trip', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('trip_number')->comment('出差编号');
			$table->integer('userid')->comment('申请人ID');
			$table->string('uname', 11)->nullable()->comment('申请人姓名');
			$table->string('dept', 100)->nullable()->comment('申请人部门');
			$table->string('position', 20)->nullable()->comment('申请人职位');
			$table->text('cause', 65535)->nullable()->comment('出差事由');
			$table->string('trip_count', 191)->nullable()->comment('出差天数');
			$table->string('trip_info', 191)->nullable()->comment('出差备注');
			$table->string('together_person', 191)->nullable()->comment('同行人');
			$table->boolean('status')->nullable()->comment('审批状态');
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
		Schema::drop('trip');
	}

}
