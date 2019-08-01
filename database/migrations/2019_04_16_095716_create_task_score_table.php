<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskScoreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_score', function (Blueprint $table) {
            $table->increments('id')->comment('自动编号');
            $table->integer('pid')->comment('任务评论id');
            $table->tinyInteger('score')->comment('任务分数');
            $table->index('pid');
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
        Schema::dropIfExists('task_score');
    }
}
