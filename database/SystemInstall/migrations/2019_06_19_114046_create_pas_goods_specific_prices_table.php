<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasGoodsSpecificPricesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_goods_specific_prices', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('goods_id')->nullable()->default(0)->index('goods_id')->comment('商品id');
			$table->string('key')->nullable()->comment('规格键名');
			$table->string('key_name')->nullable()->comment('规格键名中文');
			$table->decimal('cost_price', 10)->default(0.00)->comment('成本价格');
			$table->decimal('price', 10)->nullable()->comment('零售价');
			$table->decimal('wholesale_price', 10)->nullable()->comment('批发价');
			$table->integer('store_count')->unsigned()->nullable()->default(0)->comment('库存数量');
			$table->integer('freeze_store_count')->nullable()->default(0)->comment('冻结库存');
			$table->string('bar_code', 32)->nullable()->default('')->comment('商品条形码');
			$table->string('sku', 128)->nullable()->default('')->comment('SKU');
			$table->string('sku_name')->nullable()->comment('sku健名中文');
			$table->integer('store_upper_limit')->nullable()->comment('库存上限');
			$table->integer('store_lower_limit')->nullable()->comment('库存下限');
			$table->timestamps();
			$table->softDeletes();
			$table->index(['goods_id','key'], 'gk');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pas_goods_specific_prices');
	}

}
