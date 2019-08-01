<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class WorkReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_reports', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->increments('id');
            $table->unsignedInteger('sender_id')->default(0)->comment('用户id');
            $table->text('complete_content')->comment('已完成工作');
            $table->string('plan_content',255)->nullable()->comment('未完成工作');
            $table->string('summary_content',255)->nullable()->comment('总结（日报没有）');
            $table->string('discuss_content',255)->nullable()->comment('需协调工作');
            $table->string('img',255)->nullable()->comment('图片');
            $table->string('accessory',255)->nullable()->comment('附件');
            $table->string('remark',255)->nullable()->comment('备注');
            $table->unsignedTinyInteger('type')->default(0)->comment('0-日报 1-周报 2-月报');
            $table->unsignedInteger('created_at')->default(0)->comment('创建时间');
            $table->unsignedInteger('updated_at')->default(0)->comment('更新时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('work_reports');
    }
}
