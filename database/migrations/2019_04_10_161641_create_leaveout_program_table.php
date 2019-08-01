<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeaveoutProgramTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leaveout_program', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->notnull()->comment('申请人员id');

            $table->integer('leaveout_id')->notnull()->comment('外出申请id');
            $table->string('shenpi_no')->notnull()->comment('审批单号');
            $table->timestamp('add_time')->notnull()->comment('创建时间');
            $table->tinyInteger('now_node')->comment('当前节点id');
            $table->tinyInteger('status')->notnull()->comment('审批状态:0 未审批完成 1已批准 -1已拒绝 3已撤销');
            $table->tinyInteger('is_edit')->comment('1修改中');
            $table->string('check_user_id',255)->notnull()->comment('审批人');
            $table->string('copy_user_id',255)->comment('抄送人id');
            $table->string('check_profile_id',255)->comment('审批职位id');
            $table->string('check_department_id',255)->comment('审批部门id');
            $table->softDeletes()->comment('0:正常 1：删除');

//            $table->integer('uid')->notnull()->comment('用户id');
//            $table->integer('type')->notnull()->comment('类型1、年假 2、其他福利');
//            $table->integer('cron_date')->default(0)->notnull()->comment('上一次脚本更新的时间');
//            $table->softDeletes();





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
        Schema::dropIfExists('leaveout_program');
    }
}
