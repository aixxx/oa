<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SalaryForm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salary_form', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('employee_num');
            $table->integer('year');
            $table->integer('month');
            $table->decimal('base',10,2)->default(0.00)->comment('基础薪资总额');
            $table->string('base_json')->nullable()->comment('基础薪资组成');
            $table->decimal('subsidy',10,2)->default(0.00)->comment('补贴总额');
            $table->string('subsidy_json')->default(0.00)->comment('补贴组成');

            $table->decimal('bonus',10,2)->default(0.00)->comment('奖励金额');
            $table->decimal('fines',10,2)->default(0.00)->comment('惩罚金额');
            $table->decimal('dividend',10,2)->default(0.00)->comment('分红金额');

            $table->decimal('should_salary',10,2)->default(0.00)->comment('应发薪资');
            $table->decimal('actual_salary',10,2)->default(0.00)->comment('实发薪资');
            $table->decimal('float_salary',10,2)->default(0.00)->comment('浮动薪资');
            $table->string('remark')->nullable()->comment('备注');
            $table->tinyInteger('is_send')->default(0)->commnent('是否已发送,1:已发送,0:未发送');
            $table->tinyInteger('is_pass')->default(0)->commnent('是否已审核,0:未审核,1:审核通过,2:被拒绝');
            $table->string('auditor_note')->nullable()->comment('审核意见');
            $table->integer('entry_id')->nullable()->comment('流程申请ID');
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
        //
    }
}
