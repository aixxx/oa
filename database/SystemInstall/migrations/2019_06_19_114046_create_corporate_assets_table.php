<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCorporateAssetsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('corporate_assets', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 150)->comment('资产名称');
			$table->string('num', 150)->comment('资产编号');
			$table->integer('attr')->comment('资产属性，1：固定资产，2：虚拟资产');
			$table->integer('cat')->comment('资产分类，固定资产（1：计算机设备，2：办公设备，3：通信设备，4：家具用具，5：其他固定资产），虚拟资产（6：软件，7：其他虚拟资产）');
			$table->integer('source')->comment('资产来源，1：购入，2：接收投资，3：调入，4：自建');
			$table->decimal('price', 10)->comment('资产价格');
			$table->string('metering', 150)->comment('计量单位');
			$table->dateTime('buy_time')->nullable()->comment('购买时间');
			$table->integer('nature')->comment('资产性质，1：折旧资产，2：增值资产');
			$table->integer('depreciation_cycle')->comment('折旧周期');
			$table->integer('depreciation_interval')->comment('折旧间隔');
			$table->integer('depreciation_method')->comment('折旧方法，1：线性，2：递减');
			$table->string('location')->comment('资产位置');
			$table->string('photo')->comment('资产照片');
			$table->timestamps();
			$table->softDeletes();
			$table->integer('status')->comment('状态，1：闲置，2：在用，3：调拨，4：维修，5：报废');
			$table->integer('department_id')->comment('部门ID');
			$table->integer('company_id')->comment('公司ID');
			$table->integer('depreciation_months')->comment('折旧月数');
			$table->integer('depreciation_status')->comment('折旧状态，1：可折旧，2：不可折旧');
			$table->dateTime('remaining_at')->nullable()->comment('折旧月份');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('corporate_assets');
	}

}
