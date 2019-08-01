<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePunishmentTemplateContentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('punishment_template_content', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned()->comment('自动编号');
			$table->bigInteger('pt_id')->unsigned()->nullable()->default(0)->comment('惩罚模板id');
			$table->integer('start')->nullable()->default(0)->comment('迟到开始次数');
			$table->integer('end')->nullable()->default(0)->comment('迟到结束次数');
			$table->integer('value')->nullable()->default(0)->comment('扣除金额');
			$table->boolean('status')->nullable()->default(1)->comment('0已删除  1正常');
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('punishment_template_content');
	}

}
