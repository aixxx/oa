<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCompanyVacationAddColumnDiscountOfSalaryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company_vacation', function (Blueprint $table) {
            //
            $table->integer('discount_salary')->nullable()->default(100)->comment('工资折扣%');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company_vacation', function (Blueprint $table) {
            //
            $table->removeColumn('discount_salary');
        });
    }
}
