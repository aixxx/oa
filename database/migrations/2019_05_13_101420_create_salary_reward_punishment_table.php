<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalaryRewardPunishmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salary_reward_punishment', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 255)->comment('奖励事由');
            $table->integer('admin_id')->length(11)->comment('申请人ID');
            $table->tinyInteger('type')->length(4)->comment('奖惩类型 1：奖励 2：惩罚');
            $table->integer('user_id')->length(11)->comment('成员');
            $table->integer('department_id')->length(11)->comment('成员');
            $table->integer('money')->length(11)->comment('奖励金额');
            $table->date('dates')->comment('奖励时间');
            $table->integer('entrise_id')->length(11)->default(0)->comment('工作流ID');
            $table->tinyInteger('status')->length(4)->default(0)->comment('审核状态 0：审核中 9：审核通过 -1：驳回');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->timestamp('deleted_at')->nullable()->comment('删除时间');
            $table->index('deleted_at');
            $table->comment = '奖惩记录';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salary_reward_punishment');
    }
}
