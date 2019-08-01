<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCorporateAssetsRelationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('corporate_assets_relation', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('assets_id')->index()->comment('资产ID');
			$table->integer('event_id')->index()->comment('资产操作ID');
			$table->integer('type')->comment('操作类型，1：领用，2：借用，3：归还，4：调拨，5：送修，6：报废，7：增值，8：折旧');
			$table->text('remarks', 65535)->comment('备注');
			$table->integer('apply_user_id')->comment('实际申请人ID');
			$table->integer('user_id')->comment('用户ID');
			$table->timestamps();
			$table->softDeletes();
			$table->integer('entry_id')->comment('审批流ID');
			$table->string('type_name', 100)->nullable()->comment('类型名称');
			$table->unique(['assets_id','event_id','type','entry_id'], 'assets_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('corporate_assets_relation');
	}

}
