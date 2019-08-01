<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterContractTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contract',function(Blueprint $table){
            $table->tinyInteger('status')->default(1)->length(3)->comment('审批状态，默认为1：未审批，2：已审批，3：已拒绝');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::dropIfExists('contract_approval');
        //
    }
}
