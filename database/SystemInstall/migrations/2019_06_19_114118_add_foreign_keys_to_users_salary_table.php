<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToUsersSalaryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('users_salary', function(Blueprint $table)
		{
			$table->foreign('relation_id')->references('id')->on('users_salary_relation')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('version')->references('salary_version')->on('contract')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('users_salary', function(Blueprint $table)
		{
			$table->dropForeign('users_salary_relation_id_foreign');
			$table->dropForeign('users_salary_version_foreign');
		});
	}

}
