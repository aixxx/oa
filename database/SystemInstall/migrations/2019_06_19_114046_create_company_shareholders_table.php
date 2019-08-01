<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCompanyShareholdersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('company_shareholders', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('company_id')->comment('关联企业');
			$table->string('name', 64)->nullable()->comment('名称');
			$table->boolean('shareholder_type')->nullable()->default(1)->comment('类型(1:自然人股东;2.法人股东)');
			$table->boolean('certificate_type')->nullable()->default(1)->comment('证照/证件类型(1:非公示项;2.非公司企业法人营业执照;3.合伙企业营业执照;4.企业法人营业执照(公司))');
			$table->string('id_number', 128)->nullable()->default('')->comment('证照/证件号码(非公示项/91230XXXX)');
			$table->timestamps();
			$table->boolean('status')->default(1)->comment('状态1.有效；2.删除');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('company_shareholders');
	}

}
