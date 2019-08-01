<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeaveoutTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leaveout', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('申请人员id');
            $table->integer('department_id')->notnull()->comment('部门id');
            $table->integer('company_id')->notnull()->comment('公司id');
            $table->string('position')->notnull()->comment('申请人职位');
            $table->string('reason', 255)->comment('外出事由');
            $table->timestamp('add_time')->notnull()->comment('申请时间');
            $table->timestamp('begin_time')->notnull()->comment('外出开始时间');
            $table->timestamp('end_time')->notnull()->comment('外出结束时间');
            $table->string('explain',255)->comment('说明');
            $table->string('address',255)->comment('外出地址');
            $table->tinyInteger('duration')->comment('外出时长');
            $table->string('revoke_reason',255)->comment('撤销原因');
            $table->tinyInteger('status')->comment('状态:默认0未审批 1已通过 2已拒绝 3已撤销');

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
        Schema::dropIfExists('leaveout');
    }
}
