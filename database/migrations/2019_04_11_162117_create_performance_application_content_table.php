<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePerformanceApplicationContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('performance_application_content', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自动编号');
            $table->tinyInteger('type')->nullable(true)->default(1)->length(1)->comment('类型1.薪资绩效申请');
            $table->unsignedBigInteger('pa_id')->nullable(true)->length(20)->comment('绩效申请记录id');
            $table->unsignedBigInteger('pt_id')->nullable(true)->length(20)->comment('绩效基础表id');
            $table->integer('sum')->length(3)->nullable(true)->comment('绩效模板中各个配置项所占百分比');
            $table->integer('value')->length(3)->nullable(true)->comment('保存的值（100）%');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `performance_application_content` comment '绩效申请审核数据表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('performance_application_content');
    }
}
