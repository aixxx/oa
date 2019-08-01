<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasStockCheckAllocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pas_stock_check_allocation', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自动编号');
            $table->integer('check_id')->length(11)->default(0)->comment('盘点id');
            $table->tinyInteger('type')->nullable(true)->default(1)->length(1)->comment('类型（1表示加数量，2表示减数量）');
            $table->unsignedBigInteger('allocation_id')->nullable(true)->length(20)->comment('货位id');
            $table->integer('number')->length(11)->default(0)->comment('数量');
            $table->tinyInteger('status')->nullable(true)->default(1)->length(1)->comment('状态 0删除');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `pas_stock_check_allocation` comment '盘点商品修改数量'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pas_stock_check_allocation');
    }
}
