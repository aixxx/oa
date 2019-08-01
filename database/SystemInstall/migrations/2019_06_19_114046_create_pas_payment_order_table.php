<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasPaymentOrderTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_payment_order', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->string('code', 30)->nullable()->default('')->comment('付款单编号');
			$table->string('p_code', 30)->nullable()->default('')->index('p_code')->comment('采购单号');
			$table->string('business_date', 20)->nullable()->default('')->comment('业务日期');
			$table->bigInteger('supplier_id')->nullable()->default(0)->comment('供应商id');
			$table->bigInteger('apply_id')->nullable()->default(0)->comment('经手人id');
			$table->string('apply_name', 20)->nullable()->default('')->comment('经手人名称');
			$table->decimal('payable_money', 10)->nullable()->default(0.00)->comment('此前应付钱');
			$table->boolean('type')->nullable()->default(0)->comment('暂未启用保留');
			$table->text('remarks', 65535)->nullable()->comment('备注');
			$table->decimal('money', 10)->nullable()->default(0.00)->comment('退货总金额');
			$table->integer('entrise_id')->nullable()->default(0)->comment('会议工作流编号');
			$table->boolean('status')->nullable()->default(1)->comment('状态');
			$table->timestamps();
			$table->bigInteger('user_id')->unsigned()->nullable()->comment('付款单的用户id');
			$table->string('supplier_name', 100)->nullable()->default('')->comment('供应商名称');
			$table->bigInteger('p_id')->unsigned()->nullable()->comment('采购订单id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pas_payment_order');
	}

}
