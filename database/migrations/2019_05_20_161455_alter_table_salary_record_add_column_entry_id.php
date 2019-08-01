<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableSalaryRecordAddColumnEntryId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('salary_record', function (Blueprint $table) {
            $table->integer('entry_id')->nullable()->default(0)->comment('流程申请ID');
            $table->integer('status_entry')->nullable()->default(0)->comment('薪资统计申请是否通过,0:进行中,1:通过');
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
