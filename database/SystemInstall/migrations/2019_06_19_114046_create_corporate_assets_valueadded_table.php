<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCorporateAssetsValueaddedTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('corporate_assets_valueadded', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('num', 150)->comment('增值单号');
			$table->dateTime('valueadded_at')->nullable()->comment('报废时间');
			$table->integer('apply_user_id')->comment('实际申请人ID');
			$table->integer('user_id')->comment('增值操作人ID');
			$table->decimal('valueadded_price', 10)->comment('增值金额');
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
		Schema::drop('corporate_assets_valueadded');
	}

}
