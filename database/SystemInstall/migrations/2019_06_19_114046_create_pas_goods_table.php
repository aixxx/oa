<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasGoodsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_goods', function(Blueprint $table)
		{
			$table->increments('goods_id')->comment('商品id');
			$table->integer('category_id')->unsigned()->default(0)->index('cat_id')->comment('分类id');
			$table->string('category_parent_id', 50)->nullable()->comment('商品类目组');
			$table->string('goods_sn', 60)->nullable()->default('')->index('goods_sn')->comment('商品编号');
			$table->string('goods_name', 120)->default('')->comment('商品名称');
			$table->smallInteger('suppliers_id')->unsigned()->nullable()->comment('供应商ID');
			$table->smallInteger('store_count')->unsigned()->default(10)->index('goods_number')->comment('库存数量');
			$table->decimal('wholesale_price', 10)->unsigned()->default(0.00)->comment('批发价');
			$table->decimal('price', 10)->unsigned()->default(0.00)->comment('售价');
			$table->decimal('cost_price', 10)->nullable()->default(0.00)->comment('商品成本价');
			$table->string('description')->nullable()->default('')->comment('商品描述');
			$table->string('thumb_img')->nullable()->comment('商品缩略图');
			$table->string('img')->nullable()->default('')->comment('商品图片');
			$table->boolean('goods_type')->nullable()->default(1)->comment('商品类型， 1商品 2服务');
			$table->boolean('goods_from')->nullable()->default(0)->comment('商品来源 1内部商品 2外部商品');
			$table->integer('brand_id')->nullable()->default(0)->comment('品牌id');
			$table->string('mnemonic')->nullable()->comment('助记符');
			$table->boolean('status')->nullable()->default(1)->comment('商品状态 0草稿 1上架 2下架');
			$table->dateTime('on_time')->default('0000-00-00 00:00:00')->comment('商品上架时间');
			$table->smallInteger('sort')->unsigned()->default(50)->index('sort_order')->comment('商品排序');
			$table->integer('sales_num')->nullable()->default(0)->comment('商品销量');
			$table->integer('back_num')->nullable()->default(0)->comment('商品退货数量');
			$table->string('barcode_scheme', 50)->nullable()->comment('条码方案');
			$table->string('remark')->nullable()->comment('备注');
			$table->string('department')->nullable()->comment('归属部门');
			$table->string('organization')->nullable()->comment('归属组织');
			$table->boolean('relate_work')->nullable()->default(0)->comment('关联工作 1关联客户 2关联项目 3关联生产');
			$table->boolean('from_system')->nullable()->default(1)->comment('添加数据的系统 1erp 2客户');
			$table->timestamps();
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pas_goods');
	}

}
