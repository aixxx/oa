<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePerformanceTemplateContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('performance_template_content', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自动编号');
			$table->tinyInteger('type')->nullable(true)->default(1)->length(1)->comment('类型（1公司基础模板  2绩效模板结果）');
            $table->unsignedBigInteger('pt_id')->nullable(true)->length(20)->comment('绩效模板数据ID');
			$table->string('title')->length('50')->nullable()->comment('模板结果标题名称或是公司基础模板的id');
			$table->integer('start')->length(3)->nullable()->default(0)->comment('开始值');
			$table->integer('end')->length(3)->nullable()->default(0)->comment('结束值');
			$table->integer('value')->length(3)->nullable()->default(0)->comment('百分比值');
			$table->tinyInteger('status')->length(1)->nullable(true)->default(1)->comment('0表示删除');
			$table->timestamps();
			$table->index('pt_id');
        });
		DB::statement("ALTER TABLE `performance_template_content` comment '绩效模板子数据表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('performance_template_content');
    }
}
