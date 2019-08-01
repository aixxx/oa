<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersSalaryTemplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_salary_template', function (Blueprint $table) {
            $table->increments('id');
            $table->string('template_name')->nullable(true)->length(255)->comment('模板名称');
            $table->unsignedBigInteger('create_salary_user_id')->nullable(true)->length(20)->comment('创建者编号');
            $table->string('create_salary_user_name')->nullable(true)->length(100)->comment('创建者名称');
            $table->unsignedInteger('company_id')->nullable(true)->length(11)->comment('公司编号');
            $table->timestamps();
            $table->softDeletes();
            $table->index('deleted_at');
        });
        DB::statement("ALTER TABLE `users_salary_template` comment '薪资模板表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_salary_template');
    }
}
