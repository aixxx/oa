<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBussinessPlansTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bussiness_plans', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned()->default(0)->index('user_id')->comment('用户id');
			$table->integer('department_id')->default(0)->index('department_id')->comment('部门id');
			$table->integer('company_id')->default(0)->comment('企业id');
			$table->string('title')->comment('计划名称');
			$table->string('month', 50)->nullable()->comment('计划月份');
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
		Schema::drop('bussiness_plans');
	}

}
