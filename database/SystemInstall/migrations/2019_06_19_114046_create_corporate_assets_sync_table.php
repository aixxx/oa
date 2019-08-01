<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCorporateAssetsSyncTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('corporate_assets_sync', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('apply_user_id')->comment('实际申请人ID');
			$table->integer('assets_id')->comment('资产ID');
			$table->integer('type')->comment('类型，1：领用，2：借用，3：归还，4：调拨，5：送修，6：报废，7：增值，8：折旧');
			$table->integer('status')->comment('状态，1：闲置，2：在用，3：调拨，4：维修，5：报废');
			$table->string('content_json')->comment('信息集合');
			$table->dateTime('confirm_at')->nullable()->comment('确认时间');
			$table->integer('entry_id')->comment('工作流ID');
			$table->timestamps();
			$table->softDeletes();
			$table->integer('user_id')->comment('用户ID');
			$table->unique(['assets_id','status','entry_id'], 'assets_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('corporate_assets_sync');
	}

}
