<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasGoodsAllocationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_goods_allocation', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('row_num')->comment('排数');
			$table->integer('capacity')->nullable()->comment('容量');
			$table->integer('status')->nullable()->default(0)->comment('状态');
			$table->integer('is_private')->nullable()->default(0)->comment('是否vip');
			$table->softDeletes();
			$table->timestamps();
			$table->string('no', 100)->nullable()->comment('货位号');
			$table->integer('warehouse_id')->nullable()->comment('仓库编号');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pas_goods_allocation');
	}

}
