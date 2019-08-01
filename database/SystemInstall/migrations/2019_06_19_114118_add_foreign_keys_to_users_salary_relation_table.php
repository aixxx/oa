<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToUsersSalaryRelationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('users_salary_relation', function(Blueprint $table)
		{
			$table->foreign('template_id')->references('id')->on('users_salary_template')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('users_salary_relation', function(Blueprint $table)
		{
			$table->dropForeign('users_salary_relation_template_id_foreign');
		});
	}

}
