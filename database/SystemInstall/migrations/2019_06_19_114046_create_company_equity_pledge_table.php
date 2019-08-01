<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCompanyEquityPledgeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('company_equity_pledge', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('company_id')->comment('关联企业');
			$table->string('code', 32)->nullable()->default('')->comment('登记编号');
			$table->string('pledgor', 64)->nullable()->default('')->comment('出质人');
			$table->string('pledgor_id_number', 128)->nullable()->default('')->comment('出质人证照/证件号码');
			$table->integer('amount')->nullable()->default(0)->comment('出质股权数额');
			$table->string('pledgee', 64)->nullable()->default('')->comment('质权人');
			$table->string('pledgee_id_number', 128)->nullable()->default('')->comment('质权人证照/证件号码');
			$table->date('register_date')->nullable()->comment('股权出质设立登记日期');
			$table->boolean('pledge_status')->nullable()->comment('出质状态(1:有效;2.无效)');
			$table->boolean('status')->default(1)->comment('状态1.有效；2.删除');
			$table->date('public_at')->nullable()->comment('公示日期');
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
		Schema::drop('company_equity_pledge');
	}

}
