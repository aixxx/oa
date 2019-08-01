<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasCostInformationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_cost_information', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned()->comment('自动编号');
			$table->boolean('type')->nullable()->default(1)->comment('状态 1采购费用信息 2采购退货费用信息');
			$table->bigInteger('code_id')->unsigned()->nullable()->comment('数据编号id');
			$table->string('title', 100)->nullable()->comment('费用类型名称');
			$table->decimal('money', 10)->nullable()->default(0.00)->comment('金额');
			$table->boolean('nature')->nullable()->default(1)->comment('结算性质 1我方垫付 2对方垫付 3我方自付');
			$table->string('nature_name', 20)->nullable()->comment('结算性质名称');
			$table->boolean('payment')->nullable()->default(1)->comment('支付方式 1.现金 2支付宝3微信支付4工商银行5农业银行6中国银行7建设银行8支付通');
			$table->string('payment_name', 20)->nullable()->comment('支付名称');
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
		Schema::drop('pas_cost_information');
	}

}
