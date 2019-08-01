<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCorporateAssetsReturnTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('corporate_assets_return', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('num', 150)->comment('归还单号');
			$table->dateTime('return_at')->nullable()->comment('归还时间');
			$table->integer('apply_user_id')->comment('实际申请人ID');
			$table->integer('user_id')->comment('归还人ID');
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
		Schema::drop('corporate_assets_return');
	}

}
