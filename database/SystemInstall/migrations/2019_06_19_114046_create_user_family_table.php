<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserFamilyTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_family', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->default(0);
			$table->text('family_relate', 65535)->comment('和家人的关系（加密');
			$table->text('family_name', 65535)->comment('家人姓名（加密）');
			$table->text('family_sex', 65535)->comment('家人性别 1:男 2：女（加密）');
			$table->timestamps();
			$table->string('deleted_at', 32)->nullable();
			$table->text('birthday', 65535)->nullable()->comment('出生年月日（加密）');
			$table->char('has_children', 1)->nullable()->comment('是否有儿女 1 无 2 有');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_family');
	}

}
