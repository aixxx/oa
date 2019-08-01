<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('user_id')->length(20)->nullable(true)->comment('用户编号');
            $table->string('user_name')->length(100)->nullable(true)->comment('用户名称');
            $table->bigInteger('create_user_id')->length(20)->nullable(true)->comment('创建者编号');
            $table->string('create_user_name')->length(100)->nullable(true)->comment('创建者名称');


            $table->bigInteger('company_id')->length(20)->nullable(true)->comment('公司编号');
            $table->string('company_name')->length(100)->nullable(true)->comment('公司名称');
            $table->integer('renew_count')->length(10)->nullable(true)->comment('续签次数');

            $table->tinyInteger('probation')->length(3)->nullable(true)->comment('试用期：1，一个月 3，三个月 6，六个月');
            $table->tinyInteger('contract')->length(3)->nullable(true)->comment('合同期：1，一年 3，三年 5，五年');

            $table->bigInteger('template_id')->length(20)->nullable(true)->comment('薪资组编号');
            $table->string('template_name')->length(100)->nullable(true)->comment('薪资组名称');
            $table->decimal('performance', 5, 2)->nullable(true)->comment('绩效薪资');
            $table->decimal('salary', 5, 2)->nullable(true)->comment('总薪资');


            $table->integer('probation_ratio')->length(5)->nullable(true)->comment('试用期薪资比例：70：70%，80：80%，90：90%');
            $table->timestamp('entry_at')->nullable()->nullable(true)->comment('入职时间');
            $table->timestamp('contract_end_at')->nullable()->nullable(true)->comment('合同结束时间');
            $table->tinyInteger('state')->length(3)->nullable(true)->comment('合同状态：1，试用期，');
            $table->tinyInteger('version')->length(3)->nullable(true)->comment('合同版本');
            $table->timestamps();
            $table->softDeletes();
            $table->index('deleted_at');
        });
        DB::statement("ALTER TABLE `contract` comment '入职合同表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contract');
    }
}
