<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasWarehousingApplyTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_warehousing_apply', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->string('code', 30)->nullable()->default('')->comment('入库单号');
			$table->string('p_code', 30)->nullable()->default('')->comment('采购单号');
			$table->string('business_date', 20)->nullable()->default('')->comment('业务日期');
			$table->bigInteger('supplier_id')->nullable()->default(0)->comment('供应商id');
			$table->bigInteger('apply_id')->nullable()->default(0)->comment('经手人id');
			$table->string('apply_name', 20)->nullable()->default('')->comment('经手人名称');
			$table->decimal('payable_money', 10)->nullable()->default(0.00)->comment('此前应付钱');
			$table->text('remarks', 65535)->nullable()->comment('备注');
			$table->boolean('status')->nullable()->default(1)->comment('状态  0草稿 1待入库未安排 2待入库 - 仓库已安排 3部分入库 4全部入库 ');
			$table->timestamps();
			$table->bigInteger('user_id')->unsigned()->nullable()->comment('数据编号id');
			$table->string('supplier_name', 40)->nullable()->comment('供应商名称');
			$table->string('goods_name')->nullable()->comment('商品名称');
			$table->decimal('money')->nullable()->comment('总金额');
			$table->bigInteger('p_id')->unsigned()->nullable()->comment('采购订单id');
			$table->integer('invoice_id')->nullable()->default(0)->comment('发货方式ID');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pas_warehousing_apply');
	}

}
