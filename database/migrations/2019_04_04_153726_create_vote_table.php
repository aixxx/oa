<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVoteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vote', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自动编号');
            $table->string('vote_title',255)->nullable()->comment('投票标题');
            $table->tinyInteger('vote_type_id')->length('3')->nullable()->comment('投票类型');
            $table->char('vote_type_name')->nullable()->comment('类型名称');
            $table->text('describe')->nullable()->comment('投票描述');
            $table->string('enclosure_url')->nullable()->comment('投票附件');
            $table->timestamp('end_at')->nullable()->comment('投票结束时间');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->timestamp('deleted_at')->nullable()->comment('删除时间');
            $table->integer('create_vote_user_id')->length(11)->nullable()->comment('用户编号');
            $table->tinyInteger('prompt_type')->length(4)->nullable()->comment('提醒方式');
            $table->integer('rule_id')->length(11)->nullable()->comment('投票规则编号');
            $table->integer('company_id')->length(11)->nullable()->comment('公司编号');
            $table->integer('department_id')->length(11)->nullable()->comment('部门编号');
            $table->integer('passing_rate')->length(5)->nullable()->comment('投票通过率');
            $table->string('user_name')->length(100)->nullable()->comment('用户名称');
            $table->tinyInteger('selection_type')->length(3)->nullable()->comment('选项类型 1：单选，2：多选');
            $table->tinyInteger('state')->default(1)->length(3)->nullable()->comment('状态 1：正常，2：已取消，3：已通过，4：无效');
            $table->integer('number')->length(11)->nullable()->comment('投票票数');
            $table->index('deleted_at');
            $table->index('create_vote_user_id');
            $table->comment = '投票表';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vote');
    }
}
