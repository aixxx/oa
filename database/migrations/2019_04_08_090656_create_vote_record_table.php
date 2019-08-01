<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVoteRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vote_record', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自动编号');
            $table->bigInteger('user_id')->nullable(true)->length(20)->comment('用户编号');
            $table->bigInteger('vo_id')->nullable(true)->length(20)->comment('选项编号');
            $table->bigInteger('v_id')->nullable(true)->length(20)->comment('投票编号');
            $table->integer('v_number')->nullable(true)->length(11)->comment('投票票数');
            $table->string('user_name')->nullable(true)->length(255)->comment('用户名称');
            $table->timestamps();
            $table->softDeletes();
            $table->index('deleted_at');
        });
        DB::statement("ALTER TABLE `vote_record` comment '投票记录表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vote_record');
    }
}
