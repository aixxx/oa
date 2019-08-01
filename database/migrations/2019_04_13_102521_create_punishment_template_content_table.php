<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePunishmentTemplateContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('punishment_template_content', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自动编号');
            $table->unsignedBigInteger('pt_id')->nullable(true)->default(0)->length(20)->comment('惩罚模板id');
            $table->integer('start')->nullable(true)->default(0)->length(2)->comment('迟到开始次数');
            $table->integer('end')->nullable(true)->default(0)->length(2)->comment('迟到结束次数');
            $table->integer('value')->nullable(true)->default(0)->length(4)->comment('扣除金额');
            $table->tinyInteger('status')->nullable(true)->default(1)->length(1)->comment('0已删除  1正常');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `punishment_template_content` comment '惩罚模板字表（迟到惩罚表）'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('punishment_template_content');
    }
}
