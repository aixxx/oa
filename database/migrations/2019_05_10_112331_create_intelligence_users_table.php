<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIntelligenceUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('intelligence_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("user_id")->nullable()->length(11)->comment('情报员id');
            $table->integer("inte_id")->nullable()->length(11)->comment('情报目标id');
            $table->char("state",2)->default(-1)->comment('状态 -1无 1同意 2 拒绝');
            $table->char("attribute",1)->comment('属性 1认领 2指派');
            $table->text("reason")->comment('理由');
            $table->text('inte_content')->nullable()->comment('情报内容');
            $table->text('inte_demand')->nullable()->comment('附件需求');
            $table->string('file_upload')->nullable()->comment('附件');
            $table->timestamp('time')->nullable()->comment('时间');
            $table->string('bank')->nullable()->comment('开户行');
            $table->string('card_num')->nullable()->comment('银行账号');
            $table->char("auditstate",1)->nullable()->comment('状态 1 审核中 2已完成');
            $table->integer("entry_id")->nullable()->length(11)->comment('申请单id');
            $table->timestamps();
            $table->softDeletes();//技术
        });
        $sql = <<<EOF
ALTER TABLE `intelligence_users` add unique index(`user_id`,`inte_id`);
EOF;
        DB::statement($sql);
        DB::statement("ALTER TABLE `intelligence_users` comment '员工情报关系资料详情表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('intelligence_users');
    }
}
