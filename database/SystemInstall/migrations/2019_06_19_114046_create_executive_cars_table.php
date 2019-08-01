<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateExecutiveCarsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('executive_cars', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('title', 20)->comment('名称');
			$table->string('car_number', 20)->comment('车牌号');
			$table->string('color', 20)->comment('颜色');
			$table->string('brand', 20)->comment('品牌');
			$table->string('type', 20)->comment('类型');
			$table->string('displacement', 20)->comment('排量');
			$table->integer('seat_size')->comment('座位数量');
			$table->string('load', 20)->nullable()->comment('载重');
			$table->string('fuel_type', 20)->comment('燃油类型');
			$table->string('engine_number', 50)->comment('发动机号');
			$table->integer('buy_money')->nullable()->comment('购买金额');
			$table->date('buy_date')->nullable()->comment('购买日期');
			$table->boolean('car_status')->comment('车辆状态');
			$table->integer('driver_id')->comment('驾驶员');
			$table->integer('department_id')->comment('所属部门');
			$table->integer('entrise_id')->nullable()->comment('工作流ID');
			$table->boolean('status')->nullable()->default(0)->comment('审核状态');
			$table->timestamps();
			$table->softDeletes()->index()->comment('删除时间');
			$table->text('remark', 65535)->comment('备注');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('executive_cars');
	}

}
