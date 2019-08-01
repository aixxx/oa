<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasSaleInvoicesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_sale_invoices', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('order_id')->unsigned()->default(0)->index('user_id')->comment('订单信息');
			$table->boolean('type')->default(1)->comment('发货方式 1物流');
			$table->integer('shipping_id')->default(0)->comment('物流id');
			$table->integer('network_id')->nullable()->default(0)->comment('网点id');
			$table->string('network_mobile', 20)->nullable()->default('')->comment('网点电话');
			$table->string('waybill_number', 20)->nullable()->comment('运货单号');
			$table->string('consignee', 50)->nullable()->comment('收件人');
			$table->string('mobile', 20)->nullable()->comment('联系电话');
			$table->decimal('money')->nullable()->default(0.00)->comment('物流费用');
			$table->string('remark')->nullable();
			$table->integer('province')->default(0)->comment('省份');
			$table->integer('city')->default(0)->comment('城市');
			$table->integer('county')->default(0)->comment('地区');
			$table->integer('twon')->nullable()->default(0)->comment('乡镇');
			$table->string('address', 250)->nullable()->default('')->comment('地址');
			$table->string('zipcode', 60)->nullable()->default('')->comment('邮政编码');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pas_sale_invoices');
	}

}
