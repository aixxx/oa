<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasPaymentOrderContentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_payment_order_content', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned()->comment('自动编号');
			$table->bigInteger('p_id')->unsigned()->nullable()->comment('采购单或是退货单编号');
			$table->bigInteger('po_id')->unsigned()->nullable()->comment('付款单编号');
			$table->boolean('type')->nullable()->default(1)->comment('状态 1采购单 2付款单 ');
			$table->softDeletes();
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
		Schema::drop('pas_payment_order_content');
	}

}
