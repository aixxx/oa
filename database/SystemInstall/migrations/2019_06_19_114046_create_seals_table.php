<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSealsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('seals', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('company_id', 32)->nullable()->comment('印章主体(公司id)');
			$table->string('seal_type', 32)->default('');
			$table->string('seal_hold_user_id', 32)->default('0')->comment('当前责任人');
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
		Schema::drop('seals');
	}

}
