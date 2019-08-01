<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAddworkFieldTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('addwork_field', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('p_id')->nullable()->comment('类型名称');
			$table->string('name')->nullable()->comment('字段名称');
			$table->string('e_name')->nullable()->comment('字段的英文名称');
			$table->string('type', 20)->nullable()->comment('字段类型，例如：输入框，选择框，后台自动获取');
			$table->string('api')->nullable()->comment('字段接口地址，没有则不填');
			$table->char('validate', 100)->nullable()->comment('验证');
			$table->string('status')->nullable()->comment('类型(1.出差申请 2.薪资 3.加班申请 4.请假 5.外出)');
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
		Schema::drop('addwork_field');
	}

}
