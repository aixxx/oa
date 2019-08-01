<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserIdToPasWarehousingApplyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pas_warehousing_apply', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(true)->length(20)->comment('数据编号id');
            $table->string('supplier_name', 40)->nullable()->comment('供应商名称');
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
