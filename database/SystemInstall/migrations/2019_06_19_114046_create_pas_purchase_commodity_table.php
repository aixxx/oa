<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasPurchaseCommodityTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_purchase_commodity', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->string('p_code', 30)->nullable()->default('')->comment('采购单号');
			$table->bigInteger('p_id')->nullable()->default(0)->comment('采购表id');
			$table->string('c_url')->nullable()->default('')->comment('图片地址');
			$table->string('c_name')->nullable()->default('')->comment('商品名称');
			$table->bigInteger('c_id')->nullable()->default(0)->comment('商品id');
			$table->integer('number')->nullable()->default(0)->comment('总数量');
			$table->decimal('money', 10)->nullable()->default(0.00)->comment('总金额');
			$table->boolean('status')->nullable()->default(1)->comment('状态');
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
		Schema::drop('pas_purchase_commodity');
	}

}
