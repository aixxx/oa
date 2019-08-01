<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_logs', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自动编号');
            $table->integer("user_id")->length(11)->comment('用户id');
            $table->integer("department_id")->length(11)->comment('部门id');
            $table->integer("outer_id")->length(11)->comment('记录外部关联id');
            $table->tinyInteger('is_rpc')->length(4)->comment('是否通过rpc传输');
            $table->string('model_name')->comment('模型，app/models/文件名');
            $table->string('title')->comment('标题');
            $table->bigInteger("amount")->length(11)->comment('金额，单位分');
            $table->string('category')->comment('类型：1->报销, 2->借款, 3->还款, 4->收款，5->支付');
            $table->tinyInteger('type')->length(4)->comment('交易类型, 1=>对内交易（收）, 2=>对内交易（支）, 3 => 对外交易（收）, 4=>对外交易（支），5=>分红支出，6=>资产');
            $table->tinyInteger('is_bill')->length(4)->comment('是否有单据');
            $table->tinyInteger('is_jysr')->length(4)->comment('是否是经有收入');
            $table->tinyInteger('in_out')->length(4)->comment('1=>应收, 2=>应付');
            $table->tinyInteger('is_more_department')->length(4)->comment('多部门分摊');
            $table->tinyInteger('status')->length(4)->comment('审核状态：1=>审核通过, 2=>财务付款完成');
            $table->timestamp('status_end_time')->comment('审核时间');
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
        Schema::dropIfExists('transaction_logs');
    }
}
