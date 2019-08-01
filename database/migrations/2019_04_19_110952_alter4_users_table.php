<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Alter4UsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('users', function (Blueprint $table) {
            $table->char('is_positive', 1)->nullable(true)->default(1)->comment('转正状态 1未转正 2 转正中 3审请描述已完成');
            $table->char('is_wage', 1)->nullable(true)->default(1)->comment('工资包状态 1未设置 2已转正 ');
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
