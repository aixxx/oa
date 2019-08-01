<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeedbackAccssoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feedback_accssory', function (Blueprint $table) {

            $table->increments('id')->comment('模板编号');
            $table->tinyInteger('status')->length(2)->comment('1：评论，2：反馈');
            $table->bigInteger('rid')->length(20)->comment('关联id');
            $table->string('name')->length(255)->comment('附件名称');
            $table->string('type')->length(255)->comment('附件类型');
            $table->integer('size')->length(11)->comment('附件大小');
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
        Schema::dropIfExists('feedback_accssory');
    }
}
