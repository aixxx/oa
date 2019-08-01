<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasSupplierTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pas_supplier', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自动编号');
            $table->unsignedBigInteger('user_id')->nullable(true)->length(20)->comment('用户编号');
            $table->string('code')->length('100')->nullable()->comment('供应商编号');
            $table->string('title')->length('100')->nullable()->comment('供应商名称');
            $table->string('mnemonic')->length('30')->nullable()->comment('助记符');
            $table->string('address')->length('100')->nullable()->comment('单位地址');
            $table->string('tel')->length('20')->nullable()->comment('单位电话');
            $table->string('fax')->length('20')->nullable()->comment('传真');
            $table->string('opening_bank')->length('50')->nullable()->comment('开户行');
            $table->string('number')->length('50')->nullable()->comment('开户行账号');
            $table->string('corporations')->length('20')->nullable()->comment('法人');
            $table->string('contacts')->length('20')->nullable()->comment('联系人');
            $table->string('phone')->length('20')->nullable()->comment('联系电话');
            $table->string('zip_code')->length('15')->nullable()->comment('邮编');
            $table->text('remark')->nullable()->comment('备注');
            $table->tinyInteger('type')->nullable(true)->default(0)->length(1)->comment('类型 0手填 1自动编号');
            $table->tinyInteger('status')->nullable(true)->default(1)->length(1)->comment('状态 0删除 1添加成功');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `pas_supplier` comment '进销存供应商表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pas_supplier');
    }
}
