<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCorporateAssetsUseTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('corporate_assets_use', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('num', 150)->comment('领用单号');
			$table->integer('apply_user_id')->comment('实际申请人ID');
			$table->integer('user_id')->comment('领用人ID');
			$table->text('remarks', 65535)->comment('备注');
			$table->integer('entry_id')->comment('工作流ID');
			$table->timestamps();
			$table->softDeletes();
			$table->dateTime('use_at')->nullable()->comment('领用时间');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('corporate_assets_use');
	}

}
