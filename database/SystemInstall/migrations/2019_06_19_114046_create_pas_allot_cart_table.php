<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasAllotCartTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_allot_cart', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('warehouse_from_id')->nullable()->comment('调出仓库id');
			$table->integer('warehouse_allocation_from_id')->nullable()->comment('调出货位id');
			$table->integer('warehouse_to_id')->nullable()->comment('调入仓库id');
			$table->integer('warehouse_allocation_to_id')->nullable()->comment('调入货位id');
			$table->date('business_date')->nullable()->comment('业务日期');
			$table->text('remark', 65535)->nullable()->comment('备注');
			$table->integer('number')->nullable()->comment('合计');
			$table->integer('create_user_id')->nullable()->comment('制单人');
			$table->integer('create_user_name')->nullable()->comment('制单人姓名');
			$table->integer('cargo_user_id')->nullable()->comment('配货人');
			$table->integer('cargo_user_name')->nullable()->comment('配货人姓名');
			$table->string('delivery_type', 100)->nullable()->comment('发货方式');
			$table->integer('status')->nullable()->comment('状态');
			$table->softDeletes();
			$table->timestamps();
			$table->string('code', 40)->nullable()->comment('调拨单编号');
			$table->bigInteger('user_id')->unsigned()->nullable()->comment('添加人id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pas_allot_cart');
	}

}
