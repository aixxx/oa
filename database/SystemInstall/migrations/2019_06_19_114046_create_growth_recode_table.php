<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGrowthRecodeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('growth_recode', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->nullable()->comment('员工id');
			$table->text('content', 65535)->nullable()->comment('记录');
			$table->text('type', 65535)->nullable()->comment('类型');
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
		Schema::drop('growth_recode');
	}

}
