<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTurnoverStatsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users_turnover_stats', function(Blueprint $table)
		{
			$table->increments('id');
			$table->date('stats_date')->nullable()->comment('统计日');
			$table->date('begin_date')->nullable()->comment('期初日');
			$table->date('end_date')->nullable()->comment('期末日');
			$table->integer('week')->nullable()->comment('当月第几周');
			$table->string('department_structure', 64)->nullable()->default('')->comment('部门体系');
			$table->string('first_level', 64)->nullable()->default('')->comment('一级部门');
			$table->integer('begin_total')->nullable()->default(0)->comment('期初人数');
			$table->integer('end_total')->nullable()->default(0)->comment('期末人数');
			$table->integer('ignore_total')->nullable()->default(0)->comment('未统计人数(包括兼职、顾问、高管、临时)');
			$table->integer('sh_join')->nullable()->default(0)->comment('上海入职人数');
			$table->integer('sh_leave')->nullable()->default(0)->comment('上海离职人数');
			$table->integer('cd_join')->nullable()->default(0)->comment('成都入职人数');
			$table->integer('cd_leave')->nullable()->default(0)->comment('成都离职人数');
			$table->integer('sz_join')->nullable()->default(0)->comment('深圳入职人数');
			$table->integer('sz_leave')->nullable()->default(0)->comment('深圳离职人数');
			$table->integer('bj_join')->nullable()->default(0)->comment('北京入职人数');
			$table->integer('bj_leave')->nullable()->default(0)->comment('北京离职人数');
			$table->integer('px_join')->nullable()->default(0)->comment('萍乡入职人数');
			$table->integer('px_leave')->nullable()->default(0)->comment('萍乡离职人数');
			$table->integer('part_time_worker')->nullable()->default(0)->comment('兼职人数');
			$table->integer('adviser')->nullable()->default(0)->comment('顾问人数');
			$table->integer('leader')->nullable()->default(0)->comment('高管人数');
			$table->integer('temporary')->nullable()->default(0)->comment('临时人数');
			$table->integer('passive_leave')->nullable()->default(0)->comment('被动离职人数');
			$table->integer('voluntary_leave')->nullable()->default(0)->comment('自愿离职人数');
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
		Schema::drop('users_turnover_stats');
	}

}
