<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasWarehousingApplyContentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_warehousing_apply_content', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->string('code', 30)->nullable()->default('')->comment('入库申请表（退货申请）');
			$table->bigInteger('p_id')->nullable()->default(0)->comment('入库申请表（退货申请）id');
			$table->bigInteger('pcc_id')->nullable()->default(0)->comment('采购商品(sku)表数据id');
			$table->integer('number')->nullable()->default(0)->comment('申请数量');
			$table->integer('r_number')->nullable()->default(0)->comment('库成功数量（退货成功数量）');
			$table->decimal('money', 10)->nullable()->default(0.00)->comment('总金额');
			$table->boolean('status')->nullable()->default(1)->comment('状态');
			$table->boolean('type')->nullable()->default(1)->comment('1申请入库  2申请退货');
			$table->timestamps();
			$table->integer('warehouse_id')->nullable()->comment('仓库ID');
			$table->bigInteger('sku_id')->unsigned()->nullable()->comment('商品sku_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pas_warehousing_apply_content');
	}

}
