<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePositionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('position', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 191)->index()->comment('职位名称');
			$table->string('is_leader', 191)->default('0')->comment('是否主管,0:职员,1:主管');
			$table->timestamps();
			$table->softDeletes()->comment('删除时间');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('position');
	}

}
