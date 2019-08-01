<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCorporateAssetsRepairTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('corporate_assets_repair', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('num', 150)->comment('送修单号');
			$table->integer('apply_user_id')->comment('实际申请人ID');
			$table->integer('user_id')->comment('送修人ID');
			$table->integer('repair_day')->comment('送修时长（天）');
			$table->string('repair_company_name')->comment('送修单位名称');
			$table->decimal('repair_cost', 10)->comment('送修费用');
			$table->text('remarks', 65535)->comment('备注');
			$table->integer('entry_id')->comment('工作流ID');
			$table->timestamps();
			$table->softDeletes();
			$table->dateTime('repair_at')->nullable()->comment('送修时间');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('corporate_assets_repair');
	}

}
