<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFinancialOrderTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('financial_order', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('order_type', 100)->nullable()->default('1')->comment('类型:1 采购 2 销售');
			$table->integer('financial_id')->nullable()->comment('财务id');
			$table->string('title', 100)->nullable()->comment('名称');
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
		Schema::drop('financial_order');
	}

}
