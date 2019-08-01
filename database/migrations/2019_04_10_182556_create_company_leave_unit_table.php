<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyLeaveUnitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_leave_unit', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('c_id')->notnull()->comment('公司id');
            $table->integer('l_id')->notnull()->comment('假期类型id');
            $table->integer('type')->notnull()->comment('规则类型');
            $table->integer('n_id')->notnull()->comment('假期规则id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_leave_unit');
    }
}
