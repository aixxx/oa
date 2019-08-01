<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFeedbackTypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('feedback_type', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('type')->comment('类型(1出差，2加班，3请假，4外出)');
			$table->softDeletes()->default(DB::raw('CURRENT_TIMESTAMP'))->comment('删除时间');
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
		Schema::drop('feedback_type');
	}

}
