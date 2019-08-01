<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSkuIdToPasWarehousingApplyContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pas_warehousing_apply_content', function (Blueprint $table) {
            $table->unsignedBigInteger('sku_id')->nullable(true)->length(20)->comment('商品sku_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pas_warehousing_apply_content', function (Blueprint $table) {
            //
        });
    }
}
