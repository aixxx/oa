<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCorporateAssetsTransferTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('corporate_assets_transfer', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('num', 150)->comment('调拨单号');
			$table->dateTime('transfer_at')->nullable()->comment('调拨时间');
			$table->integer('apply_user_id')->comment('实际申请人ID');
			$table->integer('apply_department_id')->comment('实际申请人部门ID');
			$table->integer('department_id')->comment('当前使用部门');
			$table->integer('user_id')->comment('当前使用人');
			$table->integer('transfer_to_user_id')->comment('当前使用人');
			$table->integer('transfer_to_department_id')->comment('当前使用人');
			$table->text('remarks', 65535)->comment('备注');
			$table->integer('entry_id')->comment('工作流ID');
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
		Schema::drop('corporate_assets_transfer');
	}

}
