<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFinancialDetailTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('financial_detail', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('financial_id')->nullable();
			$table->decimal('money', 10)->nullable()->default(0.00)->comment('金额');
			$table->string('projects_id', 100)->nullable()->comment('项目id');
			$table->date('repayment_date')->nullable()->comment('还款日期');
			$table->string('projects_condition')->nullable()->comment('项目条件');
			$table->string('reason')->nullable()->comment('报销事由');
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
		Schema::drop('financial_detail');
	}

}
