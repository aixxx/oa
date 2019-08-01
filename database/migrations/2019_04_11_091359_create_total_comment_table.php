<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTotalCommentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('total_comment', function (Blueprint $table) {

            $table->increments('id')->comment('自动编号');
            $table->tinyInteger('type')->notnull()->comment('类型(1出差，2加班，3请假，4外出，5反馈)');
            $table->bigInteger('audit_id')->comment('审批记录id');
            $table->integer('relation_id')->notnull()->comment('关联id');
            $table->integer('uid')->notnull()->comment('用户id');
            $table->string('user_name')->notnull()->comment('用户名称');
            $table->string('comment_text')->notnull()->comment('文字评论');
            $table->string('comment_img')->notnull()->comment('图片评论');
            $table->string('comment_field')->notnull()->comment('评价附件');
            $table->timestamp('comment_time')->notnull()->comment('评价时间');
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
        Schema::dropIfExists('total_comment');
    }
}
