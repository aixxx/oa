<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAddworkCompanyTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('addwork_company', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned()->comment('自动编号');
			$table->integer('company_id')->nullable()->comment('公司编号');
			$table->integer('field_id')->nullable()->index()->comment('字段编号');
			$table->boolean('type')->nullable()->comment('1:出差，2：薪资，3：加班，4：请假');
			$table->timestamps();
			$table->softDeletes()->index();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('addwork_company');
	}

}
