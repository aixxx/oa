<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnDepartmentIdToCorporateAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('corporate_assets', function (Blueprint $table) {
            //
            $table->integer('department_id')->length(11)->comment('部门ID');
            $table->integer('company_id')->length(11)->comment('公司ID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('corporate_assets', function (Blueprint $table) {
            //
        });
    }
}
