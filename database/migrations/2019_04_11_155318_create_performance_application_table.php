<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePerformanceApplicationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('performance_application', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自动编号');
            $table->tinyInteger('type')->nullable(true)->default(1)->length(1)->comment('类型1.薪资绩效申请');
            $table->string('title')->length('50')->nullable()->comment('绩效申请名称（2018年9月绩效考核申请）');
            $table->unsignedBigInteger('user_id')->nullable(true)->length(20)->comment('申请人编号');
            $table->unsignedBigInteger('pt_id')->nullable(true)->length(20)->comment('绩效模板数据ID');
            $table->text('content')->nullable()->comment('绩效申请内容');
            $table->integer('fraction')->length(3)->nullable(true)->comment('审核分数（80%）');
            $table->integer('result')->length(3)->nullable(true)->comment('审核结果（80%）');
            $table->tinyInteger('status')->nullable(true)->default(0)->length(1)->comment('0待审核  1审核成功  2驳回重新填写');
            $table->timestamps();
            $table->index('user_id');
            $table->index('pt_id');
        });
        DB::statement("ALTER TABLE `performance_application` comment '绩效申请记录表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('performance_application');
    }
}
