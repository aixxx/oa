<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttendanceWorkflowRetroactivesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attendance_workflow_retroactives', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('entry_id')->nullable()->unique('entry_id_unique')->comment('流程id');
			$table->integer('user_id')->nullable()->index('idx_user_id')->comment('发起人id');
			$table->string('title', 128)->comment('标题');
			$table->dateTime('retroactive_datatime')->comment('补签时间');
			$table->string('retroactive_type', 128)->comment('上下班类型');
			$table->timestamps();
			$table->dateTime('finished_at')->nullable()->index('idx_finished_at')->comment('审批完成时间');
			$table->text('retroactive_reason', 65535)->nullable()->comment('补签原因');
			$table->text('note', 65535)->nullable()->comment('备注');
			$table->integer('file_upload')->nullable()->default(0)->comment('上传附件');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('attendance_workflow_retroactives');
	}

}
