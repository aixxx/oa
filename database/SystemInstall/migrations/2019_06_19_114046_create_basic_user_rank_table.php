<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBasicUserRankTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('basic_user_rank', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned()->comment('自动编号');
			$table->string('title', 100)->comment('职级名称');
			$table->text('info', 65535)->comment('描述');
			$table->boolean('level')->comment('职级的权重');
			$table->timestamps();
			$table->softDeletes()->index()->comment('删除时间');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('basic_user_rank');
	}

}
