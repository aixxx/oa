<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBasicOaTypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('basic_oa_type', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned()->comment('自动编号');
			$table->string('title', 50)->unique()->comment('类型名称');
			$table->string('code', 30)->unique()->comment('类型编号');
			$table->boolean('status')->comment('状态，1：启用，2：停用');
			$table->string('describe')->nullable()->comment('描述');
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
		Schema::drop('basic_oa_type');
	}

}
