<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePunishmentTemplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('punishment_template', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自动编号');
            $table->unsignedBigInteger('user_id')->nullable(true)->length(20)->comment('用户编号');
            $table->unsignedBigInteger('company_id')->nullable(true)->length(20)->comment('公司编号');
            $table->string('title')->length('100')->nullable()->comment('惩罚模板名称');
            $table->integer('penalty_multiple')->length('1')->default(0)->nullable(true)->comment('旷工扣除薪资倍数');
            $table->tinyInteger('status')->nullable(true)->default(1)->length(1)->comment('0已删除  1正常');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `punishment_template` comment '惩罚模板'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('punishment_template');
    }
}
