<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdministrativeContractTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('administrative_contract', function (Blueprint $table) {
            $table->increments('id')->comment('自动编号');
            $table->string("title",100)->comment('合同标题');
            $table->string("contract_number",255)->comment('合同编号');
            $table->string("primary_dept",30)->nullable(true)->comment('所属部门');
            $table->string("contract_type",30)->nullable(true)->comment('合同类型');
            $table->integer("entryId")->comment('关联工作');
            $table->integer("entry_id")->comment('审批流id');
            $table->string("secret_level",30)->nullable(true)->comment('秘密等级');
            $table->string("urgency",30)->nullable(true)->comment('紧急程度');
            $table->string("main_dept",30)->nullable(true)->comment('主送部门');
            $table->string("copy_dept",30)->nullable(true)->comment('抄送部门');
            $table->text("content")->nullable(true)->comment('合同内容');
            $table->string("file_upload",255)->nullable(true)->comment('附件');
            $table->integer('status')->length(2)->default(-1)->comment('行政合同状态，-1 不同意 1 同意');
            $table->string('process_userId',50)->nullable(true)->comment('流程全部员工id');
            $table->integer('user_id')->nullable(true)->comment('用户id');
            $table->softDeletes();
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `administrative_contract` comment '行政合同表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('administrative_contract');
    }
}
