<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVoteRuleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vote_rule', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自动编号');
            $table->string('rule_name')->nullable(true)->length(100)->comment('规则名称');
            $table->tinyInteger('is_show')->nullable(true)->length(4)->comment('是否隐藏 1，否 2，是');
            $table->integer('passing_rate')->nullable(true)->length(11)->comment('投票通过率');
            $table->integer('vote_number')->nullable(true)->length(11)->comment('投票票数');
            $table->integer('job_grade')->nullable(true)->length(11)->comment('职级编号');
            $table->timestamps();
            $table->softDeletes();
            $table->index('deleted_at');
        });
        DB::statement("ALTER TABLE `vote_rule` comment '投票规则表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vote_rule');
    }
}
