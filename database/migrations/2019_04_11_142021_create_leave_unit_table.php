<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeaveUnitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leave_unit', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uname')->length(100)->comment('请假单位名称');
            $table->integer('type')->notnull()->comment('类型 1:最小请假单位 2:计算请假时长方式 3:余额发放形式 4:有效期规则 5:新员工何时可以请假');
            $table->softDeletes()->comment();
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
        Schema::dropIfExists('leave_unit');
    }
}
