<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddworkAuditPeoplesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addwork_audit_peoples', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('addwork_id')->length(20)->comment('加班申请id');
            $table->bigInteger('user_id')->length(20)->comment('抄送人或者审批人的id');
            $table->string('user_name')->length(50)->comment('抄送人或者审批人的姓名');
            $table->tinyInteger('hierarchy')->length(2)->comment('审批人的层级');
            $table->tinyInteger('type')->length(2)->comment('1：审批人，2：抄送人');
            $table->timestamp('deleted_at')->comment('删除时间');
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
        Schema::dropIfExists('addwork_audit_peoples');
    }
}
