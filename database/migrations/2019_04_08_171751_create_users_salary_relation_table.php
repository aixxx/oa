<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersSalaryRelationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_salary_relation', function (Blueprint $table) {
            $table->increments('id')->comment('自动编号');
            $table->unsignedInteger('template_id')->nullable(true)->length(10)->comment('模板id');
            $table->unsignedInteger('field_id')->nullable(true)->length(10)->comment('字典字段id');
            $table->tinyInteger('status')->nullable(true)->length(3)->comment('类型：1，薪资 2，补贴 3，奖金');
            $table->timestamps();
            $table->softDeletes();
            $table->index('deleted_at');
            $table->foreign('template_id')->references('id')->on('users_salary_template');

        });
        DB::statement("ALTER TABLE `users_salary_relation` comment '薪资模板字典关联表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_salary_relation');
    }
}
