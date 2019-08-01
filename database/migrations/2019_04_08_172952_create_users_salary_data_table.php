<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersSalaryDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_salary_data', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('template_id')->nullable(true)->length(10)->comment('模板编号');
            $table->unsignedInteger('relation_id')->nullable(true)->length(10)->comment('关联编号');
            $table->unsignedInteger('field_id')->nullable(true)->length(10)->comment('字段编号');
            $table->unsignedInteger('company_id')->nullable(true)->length(10)->comment('公司编号');
            $table->string('field_name')->nullable(true)->length(200)->comment('字段名称');
            $table->string('field_data')->nullable(true)->length(200)->comment('字段数据');
            $table->unsignedBigInteger('user_id')->nullable(true)->length(20)->comment('用户编号');
            $table->unsignedBigInteger('contract_id')->nullable(true)->length(20)->comment('模板id');
            $table->unsignedBigInteger('create_salary_user_id')->nullable(true)->length(20)->comment('创建者编号');
            $table->string('create_salary_user_name')->nullable(true)->length(100)->comment('创建者名称');
            $table->tinyInteger('type')->nullable(true)->length(3)->comment('数据所属，1，薪资模板 2，合同设置');
            $table->timestamps();
            $table->softDeletes();
            $table->index('deleted_at');
            $table->foreign('relation_id')->references('id')->on('users_salary_relation');
        });
        DB::statement("ALTER TABLE `users_salary_data` comment '薪资数据表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_salary_data');
    }
}
