<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBussinessCategoryPlansTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bussiness_category_plans', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned()->default(0)->index('user_id')->comment('用户id');
			$table->integer('company_id')->comment('企业id');
			$table->integer('department_id')->index('department_id')->comment('部门id');
			$table->integer('plan_id')->comment('计划id');
			$table->integer('category_id')->default(0)->index('category_id')->comment('类目id');
			$table->string('content')->nullable()->comment('计划内容');
			$table->decimal('money', 12)->nullable()->default(0.00)->comment('计划金额');
			$table->string('unit_type', 50)->nullable()->comment('来往单位类型');
			$table->integer('unit')->nullable()->comment('来往单位id');
			$table->integer('program_id')->nullable()->default(0)->comment('项目id');
			$table->softDeletes();
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
		Schema::drop('bussiness_category_plans');
	}

}
