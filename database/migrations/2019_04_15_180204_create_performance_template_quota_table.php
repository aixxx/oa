<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePerformanceTemplateQuotaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('performance_template_quota', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自动编号');
            $table->string('title')->length('100')->nullable()->comment('指标名称');
            $table->string('standard')->length('100')->nullable()->comment('考核标准');
            $table->Integer('weight')->length('100')->nullable()->comment('权重');
            $table->Integer('value')->length('100')->nullable()->comment('目标值');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `performance_template_quota` comment '绩效模板维度-指标表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('performance_template_quota');
    }
}
