<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasWarehouseGoodsLocationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_warehouse_goods_location', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('warehouse_id')->comment('仓库ID');
			$table->integer('goods_allocation_id')->comment('货位ID');
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
		Schema::drop('pas_warehouse_goods_location');
	}

}
