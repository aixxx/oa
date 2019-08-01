<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasStockCheckTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pas_stock_check', function (Blueprint $table) {
            $table->increments('id');
            $table->string('check_no',100)->nullable()->comment('盘点no');
            $table->integer('warehouse_id')->nullable()->comment('仓库id');
            $table->integer('check_user_id')->nullable()->comment('盘点人');
            $table->integer('number')->nullable()->comment('盘点数量');
            $table->text('remark')->nullable()->comment('备注');
            $table->softDeletes();
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
        Schema::dropIfExists('pas_stock_check');
    }
}
