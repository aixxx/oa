<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIntelligenceInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('intelligence_info', function (Blueprint $table) {
            $table->increments('id');
            $table->text('inte_content')->nullable()->comment('情报内容');
            $table->text('inte_demand')->nullable()->comment('附件需求');
            $table->string('file_upload')->nullable()->comment('附件');
            $table->timestamp('time')->nullable()->comment('时间');
            $table->string('bank')->nullable()->comment('开户行');
            $table->string('card_num')->nullable()->comment('银行账号');
            $table->char("auditstate",1)->comment('状态 1 审核中 2已完成');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `intelligence_info` comment '员工情报资料详情表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('intelligence_info');
    }
}
