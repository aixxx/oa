<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeToPunishmentTemplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('punishment_template', function (Blueprint $table) {
            $table->tinyInteger('type')->default('1')->length(1)->comment('类型 1表示加班费 2迟到扣费 3旷工扣费 4请假');
            $table->tinyInteger('types')->default('1')->length(1)->comment('小类型区分');
            $table->tinyInteger('overtime_type')->default('1')->comment('加班类型1 工作日加班 2休息日加班  3节假日加班');
            $table->decimal('money',10,2)->nullable(true)->comment('支付金额 扣款金额（有百分比有直接是金额）');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('punishment_template', function (Blueprint $table) {
            //
        });
    }
}
