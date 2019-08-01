<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePerformanceApplicationSonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('performance_application_son', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自动编号');
            $table->bigInteger('auditor_id')->length(1)->nullable(true)->default(0)->comment('审核人id');
            $table->bigInteger('pa_id')->length('20')->nullable()->comment('绩效申请记录id');
            $table->bigInteger('pts_id')->length('20')->nullable()->comment('绩效模板维度id');
            $table->string('ptq_id')->length('100')->nullable()->comment('绩效模板维度下的指标id（2,3）');
            $table->string('completion_value')->length('50')->nullable()->comment('完成值（2|3）');
            $table->string('completion_rate')->length('50')->nullable()->comment('完成率（25|30）');
            $table->string('score')->length('50')->nullable()->comment('分值（50|30）');
            $table->tinyInteger('status')->length(1)->nullable(true)->default(0)->comment('审核状态 0未打分  1打分成功');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `performance_application_son` comment '绩效申请记录维度数据表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('performance_application_son');
    }
}
