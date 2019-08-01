<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTotalAuditTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('total_audit', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('type')->notnull()->comment('类型(1出差，2加班，3请假，4外出)');
            $table->integer('relation_id')->notnull()->comment('关联id');
            $table->integer('uid')->notnull()->comment('审批人id');
            $table->string('user_name')->length(100)->comment('审批人姓名');
            $table->integer('status')->notnull()->comment('审批状态（-1拒绝,1同意）');
            $table->timestamp('audit_time')->comment('审批时间');
            $table->integer('create_user_id')->notnull()->comment('创建者id');
            $table->integer('is_success')->notnull()->comment('是否完成 （-1作废或撤销，0默认，1完成）');

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
        Schema::dropIfExists('total_audit');
    }
}
