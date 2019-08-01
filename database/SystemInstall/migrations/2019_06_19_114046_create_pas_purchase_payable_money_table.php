<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasPurchasePayableMoneyTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_purchase_payable_money', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned()->comment('自动编号');
			$table->boolean('type')->nullable()->default(1)->comment('状态 1表示采购单');
			$table->bigInteger('supplier_id')->unsigned()->nullable()->default(0)->comment('供应商id');
			$table->decimal('money', 10)->nullable()->default(0.00)->comment('金额');
			$table->boolean('status')->nullable()->default(1)->comment('状态 1 0删除');
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
		Schema::drop('pas_purchase_payable_money');
	}

}
