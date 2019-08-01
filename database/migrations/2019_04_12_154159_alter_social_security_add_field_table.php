<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSocialSecurityAddFieldTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('social_security', function (Blueprint $table) {
            $table->renameColumn('proportion', 'company_proportion')->comment('公司缴纳比例')->change();
            $table->addColumn('string','personal_proportion')->length(100)->comment('个人缴纳比例');
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
