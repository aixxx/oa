<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasCategorysTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_categorys', function(Blueprint $table)
		{
			$table->increments('auto_id');
			$table->integer('id')->default(0)->unique('id_index')->comment('分类Id');
			$table->string('name', 191)->unique('name')->comment('分类名称');
			$table->integer('parent_id')->comment('父级id');
			$table->integer('sort')->comment('排序');
			$table->boolean('deepth')->nullable()->comment('深度');
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
		Schema::drop('pas_categorys');
	}

}
