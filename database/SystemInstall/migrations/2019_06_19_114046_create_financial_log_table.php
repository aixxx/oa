<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFinancialLogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('financial_log', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned()->comment('财务统计日志');
			$table->integer('financial_id')->nullable()->comment('财务id');
			$table->integer('operator')->nullable()->comment('操作人');
			$table->boolean('status')->nullable()->default(1)->comment('状态：1：启用');
			$table->string('remarks')->nullable()->comment('操作备注');
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
		Schema::drop('financial_log');
	}

}
