<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWelfareReceiverTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('welfare_receiver', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('welfare_id')->nullable()->index('welfare_id')->comment('福利id');
			$table->integer('minister')->nullable()->comment('主管id');
			$table->integer('user_id')->nullable()->comment('领取人id');
			$table->boolean('status')->nullable()->comment('状态：1 申请中 2 申请通过 3 申请拒绝');
			$table->boolean('is_draw')->nullable()->comment('是否领取: 1 未领取  2 已领取');
			$table->string('reason', 100)->nullable()->comment('说明');
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
		Schema::drop('welfare_receiver');
	}

}
