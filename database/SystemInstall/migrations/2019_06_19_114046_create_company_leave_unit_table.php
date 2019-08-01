<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCompanyLeaveUnitTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('company_leave_unit', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('c_id')->comment('公司id');
			$table->integer('l_id')->comment('假期类型id');
			$table->integer('type')->comment('规则类型');
			$table->integer('n_id')->comment('假期规则id');
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
		Schema::drop('company_leave_unit');
	}

}
