<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasWarehouseDeliveryTypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_warehouse_delivery_type', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('delivery_type', 45)->nullable()->comment('发货方式');
			$table->integer('logistics_id')->nullable()->comment('物流ID');
			$table->integer('point')->nullable()->comment('网点');
			$table->string('point_tel', 45)->nullable()->comment('网点电话');
			$table->string('delivery_no', 100)->nullable()->comment('运单号');
			$table->integer('receiver')->nullable()->comment('收件人');
			$table->integer('customer_info_id')->nullable()->comment('客户信息ID');
			$table->integer('status')->nullable()->comment('状态');
			$table->string('address')->nullable()->comment('收件地址');
			$table->string('contact_tel', 100)->nullable()->comment('联系电话');
			$table->text('freight_desc', 65535)->nullable()->comment('运费说明');
			$table->timestamps();
			$table->integer('user_id')->nullable()->comment('用户ID');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pas_warehouse_delivery_type');
	}

}
