<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasStockCheckTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_stock_check', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('check_no', 100)->nullable()->comment('盘点no');
			$table->integer('warehouse_id')->nullable()->comment('仓库id');
			$table->integer('check_user_id')->nullable()->comment('盘点人');
			$table->integer('number')->nullable()->comment('盘点数量');
			$table->text('remark', 65535)->nullable()->comment('备注');
			$table->softDeletes();
			$table->timestamps();
			$table->integer('status')->nullable()->comment('状态');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pas_stock_check');
	}

}
