<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasWarehouseTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_warehouse', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned()->comment('自动编号');
			$table->bigInteger('user_id')->unsigned()->nullable()->comment('创建仓库用户编号');
			$table->string('title', 100)->nullable()->comment('仓库名称');
			$table->string('alias', 100)->nullable()->comment('仓库别名');
			$table->bigInteger('charge_id')->unsigned()->nullable()->comment('负责人id');
			$table->string('charge_name', 100)->nullable()->comment('负责人姓名');
			$table->decimal('warehouse_area')->nullable()->comment('仓库面积');
			$table->string('address', 100)->nullable()->comment('仓库地址');
			$table->integer('stwarehouse')->nullable()->default(0)->comment('仓库货位');
			$table->integer('row_number')->nullable()->default(0)->comment('仓库排数');
			$table->string('telephone', 20)->nullable()->comment('联系电话');
			$table->boolean('status')->nullable()->default(1)->comment('状态 0已删除 1启用 2停用 ');
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
		Schema::drop('pas_warehouse');
	}

}
