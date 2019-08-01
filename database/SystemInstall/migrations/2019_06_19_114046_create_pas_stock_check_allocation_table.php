<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasStockCheckAllocationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_stock_check_allocation', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned()->comment('自动编号');
			$table->integer('check_id')->default(0)->comment('盘点id');
			$table->boolean('type')->nullable()->default(1)->comment('类型（1表示加数量，2表示减数量）');
			$table->bigInteger('allocation_id')->unsigned()->nullable()->comment('货位id');
			$table->integer('number')->default(0)->comment('数量');
			$table->boolean('status')->nullable()->default(1)->comment('状态 0删除');
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
		Schema::drop('pas_stock_check_allocation');
	}

}
