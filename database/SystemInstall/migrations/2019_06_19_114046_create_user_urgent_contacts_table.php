<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserUrgentContactsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_urgent_contacts', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->default(0);
			$table->text('relate', 65535)->comment('和联系人的关系（加密）');
			$table->text('relate_name', 65535)->comment('联系人姓名（加密）');
			$table->text('relate_phone', 65535)->comment('联系人电话（加密）');
			$table->timestamps();
			$table->string('deleted_at', 32)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_urgent_contacts');
	}

}
