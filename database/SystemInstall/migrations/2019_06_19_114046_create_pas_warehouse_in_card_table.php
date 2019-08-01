<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasWarehouseInCardTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_warehouse_in_card', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('type')->nullable()->comment('申请单 类型 1、采购入库  2、退货入库 3、调货入库');
			$table->string('in_no', 100)->nullable()->comment('入库单号');
			$table->integer('apply_id')->nullable()->comment('申请单ID');
			$table->integer('goods_allocation_id')->nullable()->comment('仓库ID');
			$table->integer('create_user_id')->nullable()->comment('制单人ID');
			$table->integer('create_user_name')->nullable()->comment('制单人名');
			$table->integer('cargo_user_id')->nullable()->comment('配货人ID');
			$table->integer('cargo_user_name')->nullable()->comment('配货人名');
			$table->string('delivery_type', 40)->nullable()->comment('送货方式');
			$table->integer('delivery_type_id')->nullable()->comment('送货方式Id');
			$table->string('delivery_name', 40)->nullable()->comment('送货人姓名');
			$table->integer('status')->nullable()->comment('状态');
			$table->integer('percent')->nullable()->comment('百分比');
			$table->text('remark', 65535)->nullable()->comment('备注');
			$table->timestamps();
			$table->integer('warehouse_id')->nullable()->comment('仓库ID');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pas_warehouse_in_card');
	}

}
