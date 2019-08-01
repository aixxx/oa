<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVoteParticipantTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vote_participant', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自动编号');
            $table->bigInteger('v_id')->length(20)->comment('投票编号');
            $table->integer('create_vote_user_id')->length(10)->nullable(true)->comment('创建人编号');
            $table->string('create_vote_user_name')->length(100)->nullable(true)->comment('创建人名称');
            $table->integer('user_id')->length(10)->nullable(true)->comment('参与人编号');
            $table->string('user_name')->length(100)->nullable(true)->comment('参与人名称');
            $table->text('describe')->nullable()->comment('投票描述');
            $table->tinyInteger('confirm_yes')->default(1)->length(3)->nullable(true)->comment('状态：1待确认，2已确认');
            $table->timestamps();
            $table->softDeletes();
            $table->index('deleted_at');
        });
        DB::statement("ALTER TABLE `vote_participant` comment '投票参与人表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vote_participant');
    }
}
