<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddworkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addwork', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->length(20)->comment('申请加班的人员id');
            $table->string('name')->length(50)->comment('申请人名字');
            $table->integer('company_id')->length(11)->comment('公司id');
            $table->integer('department_id')->length(11)->comment('部门id');
            $table->string('position')->length(255)->comment('职位');
            $table->string('numbers')->length(255)->comment('审批编号');
            $table->dateTime('add_time')->comment('申请时间');
            $table->dateTime('begin_time')->comment('开始时间');
            $table->dateTime('end_time')->comment('结束时间');
            $table->integer('duration')->length(10)->comment('时长');
            $table->string('cause')->length(255)->comment('加班原因');
            $table->tinyInteger('status')->default(2)->length(2)->comment('申请状态，默认为2：未审批完，3：已同意，4：已拒绝，5：已撤销');
            $table->string('revocation_cause')->length(255)->comment('撤销原因');
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
        Schema::dropIfExists('addwork');
    }
}
