<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUsersTableSetSomecolumnsDefaultNull extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
            $table->integer('employee_num')->nullable()->change();
            $table->string('english_name',255)->nullable()->change();
//            $table->text('mobile')->nullable()->change();
            $table->string('position',255)->nullable()->change();
            $table->dropUnique(array('employee_num'));
            $table->dropUnique(array('name'));
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
