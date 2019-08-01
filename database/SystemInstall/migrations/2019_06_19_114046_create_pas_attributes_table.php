<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasAttributesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_attributes', function(Blueprint $table)
		{
			$table->increments('id')->comment('属性id');
			$table->string('name', 60)->default('')->comment('属性名称');
			$table->boolean('type')->nullable()->default(1)->comment('属性值类型 1单选 2多选');
			$table->text('values', 65535)->comment('可选值列表');
			$table->boolean('sort')->default(50)->comment('属性排序');
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
		Schema::drop('pas_attributes');
	}

}
