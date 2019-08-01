<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCompanyAnnualRuleTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('company_annual_rule', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('company_id')->comment('公司id');
			$table->integer('rule_id')->comment('公司id');
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
		Schema::drop('company_annual_rule');
	}

}
