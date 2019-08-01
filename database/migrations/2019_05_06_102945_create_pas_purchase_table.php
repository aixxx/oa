<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasPurchaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pas_purchase', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自动编号');
            $table->unsignedBigInteger('user_id')->nullable(true)->length(20)->comment('申请用户编号');
            $table->string('code')->length('30')->nullable()->comment('供应商编号');
            $table->string('business_date')->length('100')->nullable()->comment('业务日期');
            $table->unsignedBigInteger('supplier_id')->nullable(true)->length(20)->comment('供应商id');
            $table->Integer('payable_money')->nullable(true)->default(0)->length(10)->comment('此前应付钱');
            $table->string('apply_name')->length('20')->nullable()->comment('经手人');
            $table->Integer('earnest_money')->nullable(true)->default(0)->length(10)->comment('定金');
            $table->Integer('number')->nullable(true)->default(0)->length(10)->comment('商品总数');
            $table->decimal('total_sum', 10, 2)->nullable(true)->comment('合计金额');
            $table->Integer('discount')->nullable(true)->default(0)->length(3)->comment('折扣');
            $table->decimal('turnover_amount', 10, 2)->nullable(true)->comment('成交金额');
            $table->text('remark')->nullable()->comment('备注');
            $table->tinyInteger('status')->nullable(true)->default(0)->length(1)->comment('状态 0草稿 1审核中 2已撤回 3已退回 5审核完成');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `pas_purchase` comment '进销存采购表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pas_purchase');
    }
}
