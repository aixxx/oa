<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalaryRewardPunishmentComplainTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salary_reward_punishment_complain', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pr_id')->length(11)->comment('奖惩ID');
            $table->string('remark',255)->comment('申诉理由');
            $table->string('remark_img', 255)->nullable()->comment('图片');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->timestamp('deleted_at')->nullable()->comment('删除时间');
            $table->index('deleted_at');
            $table->comment = '奖惩申诉记录';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salary_reward_punishment_complain');
    }
}
