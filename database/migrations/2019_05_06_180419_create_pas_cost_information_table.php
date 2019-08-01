<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasCostInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pas_cost_information', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自动编号');
            $table->tinyInteger('type')->nullable(true)->default(1)->length(1)->comment('状态 1采购费用信息 2采购退货费用信息');
            $table->unsignedBigInteger('code_id')->nullable(true)->length(20)->comment('数据编号id');
            $table->string('title')->length('100')->nullable(true)->comment('费用类型名称');
            $table->decimal('money', 10, 2)->default(0)->nullable(true)->comment('金额');
            $table->tinyInteger('nature')->nullable(true)->default(1)->length(1)->comment('结算性质 1我方垫付 2对方垫付 3我方自付');
            $table->string('nature_name')->length('20')->nullable(true)->comment('结算性质名称');
            $table->tinyInteger('payment')->nullable(true)->default(1)->length(1)->comment('支付方式 1.现金 2支付宝3微信支付4工商银行5农业银行6中国银行7建设银行8支付通');
            $table->string('payment_name')->length('20')->nullable(true)->comment('支付名称');
            $table->tinyInteger('status')->nullable(true)->default(1)->length(1)->comment('状态 1 0删除');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `pas_cost_information` comment '进销存费用信息'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pas_cost_information');
    }
}
