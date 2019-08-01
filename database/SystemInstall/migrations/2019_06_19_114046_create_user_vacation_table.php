<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserVacationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_vacation', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->comment('用户');
			$table->integer('annual_time')->nullable()->default(0)->comment('年假（分钟）');
			$table->integer('rest_time')->nullable()->default(0)->comment('调休（分钟）');
			$table->integer('menstrual_time')->nullable()->default(0)->comment('例假（分钟）');
			$table->smallInteger('maternity_cnt')->nullable()->default(0)->comment('请过产假次数');
			$table->smallInteger('paternity_cnt')->nullable()->default(0)->comment('请过陪产假次数');
			$table->smallInteger('marital_cnt')->nullable()->default(0)->comment('请过婚假次数');
			$table->smallInteger('breastfeeding_cnt')->nullable()->default(0)->comment('请过哺乳假次数');
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
		Schema::drop('user_vacation');
	}

}
