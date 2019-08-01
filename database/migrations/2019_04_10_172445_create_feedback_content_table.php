<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeedbackContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feedback_content', function (Blueprint $table) {
            $table->increments('id')->comment('自动编号');
            $table->tinyInteger('tid')->nullable(true)->length(4)->comment('反馈类型id');
            $table->string('title')->nullable(true)->length(255)->comment('标题');
            $table->string('content')->nullable(true)->length(255)->comment('内容');
            $table->tinyInteger('way')->nullable(true)->length(4)->comment('2：匿名反馈，3：实名反馈');
            $table->dateTime('publish_time')->comment('发布时间');
            $table->tinyInteger('status')->nullable(true)->length(4)->comment('2：未回复，3：已回复未读，4：回复已读');
            $table->bigInteger('uid')->nullable(true)->comment('发布人员id');
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
        Schema::dropIfExists('feedback_content');
    }
}
