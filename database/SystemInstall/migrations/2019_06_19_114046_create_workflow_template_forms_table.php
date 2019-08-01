<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWorkflowTemplateFormsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('workflow_template_forms', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('template_id');
			$table->string('field', 64);
			$table->string('field_name', 64);
			$table->string('placeholder', 191)->comment('placeholder');
			$table->string('field_type', 64);
			$table->text('field_value', 65535);
			$table->string('field_default_value', 64);
			$table->string('field_extra_css')->default('')->comment('额外的css样式类');
			$table->string('unit', 64)->nullable()->comment('单位');
			$table->text('rules', 65535);
			$table->integer('sort');
			$table->timestamps();
			$table->integer('location')->default(0)->comment('栅格化');
			$table->integer('required')->default(0)->comment('必填项');
			$table->boolean('show_in_todo')->comment('是否在待办事项');
			$table->integer('length')->unsigned()->default(0)->comment('字段值的长度');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('workflow_template_forms');
	}

}
