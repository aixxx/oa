<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVoteDepartmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vote_department', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->length(10)->comment('公司编号');
            $table->integer('department_id')->length(10)->comment('部门编号');
            $table->integer('v_id')->length(10)->comment('投票编号');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `vote_department` comment '投票参与部门'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vote_department');
    }
}
