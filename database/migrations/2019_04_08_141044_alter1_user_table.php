<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Alter1UserTable extends Migration
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
            $table->char('is_person_perfect', 2)->default(0)->nullable(true)->comment('身份信息 0不完善 17完善');
            $table->char('is_card_perfect', 2)->default(0)->nullable(true)->comment('银行卡信息 0不完善 17完善');
            $table->char('is_edu_perfect', 2)->default(0)->nullable(true)->comment('学历信息 0不完善 17完善');
            $table->char('is_pic_perfect', 2)->default(0)->nullable(true)->comment('个人材料 0不完善 17完善');
            $table->char('is_family_perfect', 2)->default(0)->nullable(true)->comment('家庭信息 0不完善 17完善');
            $table->char('is_urgent_perfect', 2)->default(0)->nullable(true)->comment('紧急联系人 0不完善 17完善');
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
