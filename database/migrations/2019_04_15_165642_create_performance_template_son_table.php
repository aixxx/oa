<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePerformanceTemplateSonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('performance_template_son', function (Blueprint $table) {
        $table->bigIncrements('id')->comment('自动编号');
        $table->unsignedBigInteger('pt_id')->nullable(true)->length(20)->comment('绩效模板编号');
        $table->string('title')->length('100')->nullable()->comment('指标维度名称');
        $table->Integer('numb')->nullable(true)->default(0)->length(5)->comment('总权重');
        $table->unsignedBigInteger('approval_id')->nullable(true)->length(20)->comment('考核人id');
        $table->tinyInteger('status')->length(1)->nullable(true)->default(1)->comment('状态');
        $table->timestamps();
    });
        DB::statement("ALTER TABLE `performance_template_son` comment '绩效模板维度表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('performance_template_son');
    }
}
