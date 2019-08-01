<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFinanceTerItemTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('finance_ter_item', function(Blueprint $table)
		{
			$table->increments('id')->comment('主键');
			$table->integer('finance_ter_id')->index('idx_finance_ter_id')->comment('差旅报销单ID');
			$table->string('description')->comment('项目事由描述');
			$table->string('travel_from')->comment('出发地');
			$table->string('travel_to')->comment('目的地');
			$table->bigInteger('telecom_amount')->unsigned()->comment('交通费用，单位为分');
			$table->bigInteger('hotel_amount')->unsigned()->comment('住宿费用，单位为分');
			$table->bigInteger('allowance_amount')->unsigned()->comment('出差补贴，单位为分');
			$table->bigInteger('other_amount')->unsigned()->comment('其他杂项，单位为分');
			$table->integer('invoice_quantity')->unsigned()->comment('单据发票张数');
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
		Schema::drop('finance_ter_item');
	}

}
