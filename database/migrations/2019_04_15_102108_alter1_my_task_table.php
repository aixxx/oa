<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Alter1MyTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('my_task', function (Blueprint $table) {
            $table->integer('status')
                ->default(0)
                ->length(3)
                ->nullable()
                ->comment('任务状态（-1拒绝,0默认,1待确认,2待办理,3待评价,4完成）')->change();
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
