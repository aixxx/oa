<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateReportsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('reports', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned()->default(0)->index('user_id')->comment('用户id');
			$table->integer('template_id')->index('template_id')->comment('模板id');
			$table->integer('company_id')->comment('企业id');
			$table->text('content', 65535)->comment('汇报模版自定义内容');
			$table->string('img')->nullable()->comment('图片');
			$table->string('accessory')->nullable()->comment('附件');
			$table->text('select_depart', 65535)->comment('选中的部门');
			$table->text('select_user', 65535)->comment('接收汇报者');
			$table->text('read', 65535)->nullable()->comment('已读人员');
			$table->string('remark')->nullable()->comment('备注');
			$table->dateTime('deleted_at')->default('0000-00-00 00:00:00')->comment('删除时间');
			$table->timestamps();
			$table->text('cc_ids', 65535)->nullable()->comment('抄送人');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('reports');
	}

}
