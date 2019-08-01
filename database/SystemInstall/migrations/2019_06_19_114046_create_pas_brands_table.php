<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasBrandsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_brands', function(Blueprint $table)
		{
			$table->smallInteger('id', true)->unsigned()->comment('品牌表');
			$table->string('name', 60)->default('')->comment('品牌名称');
			$table->string('logo', 80)->default('')->comment('品牌logo');
			$table->text('desc', 65535)->comment('品牌描述');
			$table->string('url')->default('')->comment('品牌地址');
			$table->boolean('sort')->default(50)->comment('排序');
			$table->boolean('status')->nullable()->default(0)->comment('0不使用 1使用');
			$table->timestamps();
			$table->softDeletes();
			$table->char('first', 5)->comment('首字母大写');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pas_brands');
	}

}
