<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVacationRuleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vacation_rule', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('unit')->nullable()->default(1)->comment('最小请假单位1、半天 2、一天 3、1小时');
            $table->integer('leave_type')->nullable()->default(1)->comment('请假类型1、工作日计算 2、自然日计算');
            $table->integer('is_balance')->nullable()->default(0)->comment('是否开启余额1、是 0否');
            $table->integer('balance_type')->nullable()
                ->comment('余额发放类型 1、每年自动固定发放天数 2、按照入职时间自动发放 3、加班时长自动计入余额');
            $table->integer('balance_value')->nullable()->default(0)->comment('余额发放数');
            $table->integer('expire_rule')->comment('有效期规则1、按自然年(1月1日 - 12月31日) 2、按入职日期12月');
            $table->integer('is_add_expire')->nullable()->default(0)->comment('是否可以延长时间');
            $table->integer('add_expire_value')->nullable()->default(0)->comment('延长时间');
            $table->integer('newer_start_type')->nullable()->default(1)->comment('新员工请假类型1、入职当天 2、转正');
            $table->integer('salary_percent')->nullable()->default(0)->comment('薪资比例');
            $table->integer('company_id')->comment('公司');
            $table->integer('cursor')->comment('操作人');
            $table->integer('status')->nullable()->default(0)->comment('状态');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vacation_rule');
    }
}
