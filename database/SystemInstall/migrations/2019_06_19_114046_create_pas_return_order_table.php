<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasReturnOrderTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_return_order', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->string('code', 30)->nullable()->default('')->comment('退货单号');
			$table->string('p_code', 30)->nullable()->default('')->comment('采购单号');
			$table->string('business_date', 20)->nullable()->default('')->comment('业务日期');
			$table->bigInteger('supplier_id')->nullable()->default(0)->comment('供应商id');
			$table->bigInteger('apply_id')->nullable()->default(0)->comment('经手人id');
			$table->string('apply_name', 20)->nullable()->default('')->comment('经手人名称');
			$table->decimal('payable_money', 10)->nullable()->default(0.00)->comment('此前应付钱');
			$table->boolean('type')->nullable()->default(0)->comment('0未入库  1表示已入库');
			$table->text('remarks', 65535)->nullable()->comment('备注');
			$table->integer('number')->nullable()->default(0)->comment('退货总数');
			$table->decimal('money', 10)->nullable()->default(0.00)->comment('退货总金额');
			$table->integer('entrise_id')->nullable()->default(0)->comment('会议工作流编号');
			$table->boolean('status')->nullable()->default(1)->comment('状态');
			$table->timestamps();
			$table->bigInteger('user_id')->unsigned()->nullable()->comment('添加退货单的用户id');
			$table->string('supplier_name', 100)->nullable()->comment('供应商名称');
			$table->bigInteger('p_id')->unsigned()->nullable()->comment('采购订单id');
			$table->boolean('p_status')->nullable()->default(0)->comment('付款状态 0未付款 1 付款申请中 2付款完成 ');
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
		Schema::drop('pas_return_order');
	}

}
