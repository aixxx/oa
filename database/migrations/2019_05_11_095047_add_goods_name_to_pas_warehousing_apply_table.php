<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGoodsNameToPasWarehousingApplyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pas_warehousing_apply', function (Blueprint $table) {
            $table->string('goods_name', 255)->nullable()->comment('商品名称');
            $table->decimal('money', 8, 2)->nullable(true)->comment('总金额');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pas_warehousing_apply', function (Blueprint $table) {
            //
        });
    }
}
