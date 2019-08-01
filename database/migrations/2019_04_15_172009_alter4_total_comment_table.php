<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Alter4TotalCommentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('total_comment', function (Blueprint $table) {

            $table->integer('type')
                ->notnull()
                ->length(5)
                ->comment('类型(1出差,2加班,3请假,4外出,5反馈,6审批)')
                ->change();
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
