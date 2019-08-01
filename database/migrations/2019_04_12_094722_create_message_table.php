<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('receiver_id')->comment('接收者');
            $table->integer('sender_id')->comment('发送者');
            $table->text('content')->comment('内容');
            $table->integer('status')->nullable()->default(0)->comment('消息状态 0、普通 1、举报');
            $table->integer('flag')->nullable()->default(0)->comment('消息标签 0、普通 1、标星');
            $table->integer('read_status')->nullable()->default(0)->comment('阅读状态 1、已阅读');
            $table->string('remark')->nullable()->default('')->comment('');
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
        Schema::dropIfExists('message');
    }
}
