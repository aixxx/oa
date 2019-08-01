<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->notnull()->comment('评分人id');
            $table->integer('o_id')->notnull()->comment('任务id');
            $table->string('score')->length(100)->comment('评分数');
            $table->string('comments')->length(100)->comment('评论');
            $table->integer('comment_time')->notnull()->comment('评论时间');
            $table->integer('type')->notnull()->comment('0：个人打分 1：自动打分；');
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
        Schema::dropIfExists('comments');
    }
}
