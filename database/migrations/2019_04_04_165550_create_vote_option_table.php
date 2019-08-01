<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVoteOptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vote_option', function (Blueprint $table) {

            $table->bigIncrements('id')->comment('自动编号');
            $table->bigInteger('v_id')->length('20')->default('0')->comment('投票编号');
            $table->string('option_name')->length('100')->nullable()->comment('投票选项名称');
            $table->tinyInteger('state')->default(1)->length(3)->nullable(true)->comment('选项状态 1，未通过 2，已通过');
            $table->integer('percentage')->length(5)->nullable(true)->comment();
            $table->timestamps();
            $table->index('v_id');
        });
        DB::statement("ALTER TABLE `vote_option` comment '投票选项表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vote_option');
    }
}
