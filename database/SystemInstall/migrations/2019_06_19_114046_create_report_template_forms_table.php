<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateReportTemplateFormsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('report_template_forms', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('template_id')->index('template_id')->comment('模板id');
			$table->string('field', 64)->comment('字段名称');
			$table->string('field_name', 64)->comment('字段展示名');
			$table->string('placeholder', 191)->comment('字段提示语');
			$table->string('field_type', 64)->comment('字段类型');
			$table->text('field_value', 65535)->comment('字段值');
			$table->string('field_default_value', 64)->comment('字段默认值');
			$table->string('field_extra_css')->default('')->comment('额外的css样式类');
			$table->string('unit', 64)->nullable()->comment('单位');
			$table->text('rules', 65535);
			$table->integer('sort')->comment('排序');
			$table->timestamps();
			$table->integer('location')->default(0)->comment('栅格化');
			$table->integer('required')->default(0)->comment('必填项');
			$table->softDeletes()->default('0000-00-00 00:00:00')->comment('删除时间');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('report_template_forms');
	}

}
