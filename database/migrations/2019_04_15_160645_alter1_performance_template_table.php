<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Alter1PerformanceTemplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('performance_template', function (Blueprint $table) {
            $table->unsignedBigInteger('type_id')->nullable(true)->length(20)->comment('员工类型');
            $table->unsignedBigInteger('department_id')->nullable(true)->length(20)->comment('员工部门');
            $table->unsignedBigInteger('object_id')->nullable(true)->length(20)->comment('考核对象');
            $table->Integer('review_time')->nullable(true)->default(0)->length(5)->comment('自评时间');
            $table->Integer('remind_time')->nullable(true)->default(0)->length(5)->comment('自评提醒时间');
            $table->Integer('money')->nullable(true)->default(0)->length(5)->comment('绩效金额');
            $table->Integer('number')->nullable(true)->default(0)->length(5)->comment('总权重');
            $table->Integer('usage_number')->nullable(true)->default(0)->length(5)->comment('使用人数');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
