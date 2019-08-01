<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractApprovalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract_approval', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contract_id')->length(10)->nullable(true)->comment('合同编号');
            $table->integer('user_id')->length(10)->nullable(true)->comment('用户编号');
            $table->tinyInteger('level')->length(3)->nullable(true)->comment('审批步数 (当前是第几步)');
            $table->integer('create_user_id')->length(10)->nullable(true)->comment('创建人编号');
            $table->timestamps();
            $table->softDeletes();
            $table->index('deleted_at');
        });
        DB::statement("ALTER TABLE `contract_approval` comment '合同审批关联表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contract_approval');
    }
}
