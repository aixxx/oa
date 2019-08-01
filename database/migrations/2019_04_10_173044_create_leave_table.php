<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeaveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leave', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->notnull()->comment('用户id');
            $table->string('anum')->length(50)->comment('审批编号');
            $table->string('sqtime')->length(50)->comment('申请时间');
            $table->integer('bm_id')->notnull()->comment('部门id');
            $table->string('job')->length(50)->comment('职位');
            $table->string('stime')->length(50)->comment('假期开始时间');
            $table->string('etime')->length(50)->comment('假期截止时间');
            $table->integer('times')->notnull()->comment('请假时长');
            $table->integer('v_id')->notnull()->comment('请假类型');
            $table->string('message')->length(255)->comment('请假事由');
            $table->string('image')->length(255)->comment('附件图片');
            $table->integer('status')->notnull()->comment('请假状态 0：审批中 1：审批通过 2：审批拒绝 3：撤销成功');
            $table->string('leavenum')->length(100)->comment('请假单号');
            $table->string('phone')->length(50)->comment('联系电话');
            $table->integer('tel')->notnull()->length(11)->comment('手机号');
            $table->string('address')->length(255)->comment('联系地址');
            $table->integer('c_id')->notnull()->length(11)->comment('公司id');
            $table->string('reason')->length(255)->comment('撤销的理由');

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
        Schema::dropIfExists('leave');
    }
}
