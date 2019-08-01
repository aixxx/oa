<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Alert1AddworkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('addwork', function (Blueprint $table) {
            $table->string('cause',255)->nullable()->change();
            $table->string('revocation_cause',255)->nullable()->change();
            $table->string('revocation_cause',255)->nullable()->change();
            $table->dropColumn('deleted_at');
            // $table->softDeletes(); // 新增一个允许为空的 deleted_at TIMESTAMP 列用于软删除
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
