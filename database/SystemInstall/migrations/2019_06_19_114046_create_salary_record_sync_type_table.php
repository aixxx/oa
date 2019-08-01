<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSalaryRecordSyncTypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('salary_record_sync_type', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('year');
			$table->integer('month');
			$table->integer('type');
			$table->string('remark', 191)->nullable()->comment('备注');
			$table->softDeletes();
			$table->timestamps();
			$table->integer('count')->default(0)->comment('人数');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('salary_record_sync_type');
	}

}
