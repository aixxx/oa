<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCtypeToPasSupplierTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pas_supplier', function (Blueprint $table) {
            $table->tinyInteger('ctype')->nullable(true)->default(1)->length(20)->comment('状态 区分经销商是 1是内部添加 2外部添加');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pas_supplier', function (Blueprint $table) {
            //
        });
    }
}
