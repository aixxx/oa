<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasWarehouseOutCardTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_warehouse_out_card', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('out_no', 100)->nullable()->comment('出库单号');
			$table->integer('out_type')->nullable()->comment('出库类型 1、调拨 2、采购退货 3、销售单');
			$table->integer('warehouse_id')->nullable()->comment('仓库ID');
			$table->integer('allocation_id')->nullable()->comment('货位ID');
			$table->integer('status')->nullable()->comment('状态');
			$table->integer('create_user_id')->nullable()->comment('制单人ID');
			$table->string('create_user_name', 100)->nullable()->comment('制单人名');
			$table->date('deliver_date')->nullable()->comment('发货日期');
			$table->date('business_date')->nullable()->comment('业务日期');
			$table->integer('deliver_type')->nullable()->comment('发货方式');
			$table->integer('number')->nullable()->comment('出库数');
			$table->text('remark', 65535)->nullable()->comment('备注');
			$table->timestamps();
			$table->integer('apply_id')->nullable()->comment('出库单申请单据ID');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pas_warehouse_out_card');
	}

}
