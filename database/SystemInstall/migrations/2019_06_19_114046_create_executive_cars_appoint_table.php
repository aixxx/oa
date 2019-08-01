<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateExecutiveCarsAppointTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('executive_cars_appoint', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('title')->comment('车辆用途');
			$table->integer('cars_id')->comment('车辆ID');
			$table->integer('driver_id')->comment('用车人ID');
			$table->integer('user_id')->comment('申请人ID');
			$table->integer('department_id')->comment('部门ID');
			$table->integer('people_number')->comment('用车人数');
			$table->date('begin_time')->comment('用车开始时间');
			$table->date('end_time')->comment('预计返回时间');
			$table->integer('mileage')->comment('预计里程数');
			$table->string('cause')->nullable()->comment('事由');
			$table->string('remark')->nullable()->comment('备注');
			$table->integer('entrise_id');
			$table->boolean('status')->comment('审核状态');
			$table->timestamps();
			$table->softDeletes()->index()->comment('删除时间');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('executive_cars_appoint');
	}

}
