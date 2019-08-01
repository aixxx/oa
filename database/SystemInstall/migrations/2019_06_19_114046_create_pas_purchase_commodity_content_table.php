<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasPurchaseCommodityContentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_purchase_commodity_content', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->string('p_code', 30)->nullable()->default('')->comment('采购单号');
			$table->bigInteger('p_id')->nullable()->default(0)->comment('采购表id');
			$table->string('sku')->nullable()->default('')->comment('sku (规则组合)');
			$table->integer('number')->nullable()->default(0)->comment('商品(sku)采购的数量');
			$table->decimal('price', 10)->nullable()->default(0.00)->comment('总数量');
			$table->decimal('money', 10)->nullable()->default(0.00)->comment('总金额');
			$table->integer('r_number')->nullable()->default(0)->comment('退货数量');
			$table->integer('war_number')->nullable()->default(0)->comment('可入库数量');
			$table->integer('wa_number')->nullable()->default(0)->comment('申请入库数量');
			$table->integer('awa_numbe')->nullable()->default(0)->comment('已经入库数量');
			$table->boolean('status')->nullable()->default(1)->comment('状态 0删除  1正常');
			$table->timestamps();
			$table->bigInteger('goods_id')->unsigned()->nullable()->comment('商品id');
			$table->string('goods_name')->nullable()->comment('商品名称');
			$table->string('goods_url')->nullable()->comment('商品图片');
			$table->string('sku_id')->nullable()->comment('sku组合id');
			$table->integer('rw_number')->nullable()->default(0)->comment('入库过后的退货');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pas_purchase_commodity_content');
	}

}
