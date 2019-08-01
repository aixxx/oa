<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateReportTemplatesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('report_templates', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('template_name', 64);
			$table->integer('user_id')->default(0)->index('user_id')->comment('创建者id');
			$table->integer('company_id')->default(0)->comment('企业id');
			$table->timestamps();
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
		Schema::drop('report_templates');
	}

}
