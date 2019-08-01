<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUsersDetailInfoPictureTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('users_detail_info', function (Blueprint $table) {
            $table->text('pic_id_pos')->comment('身份证（人像面）')->change();
            $table->text('pic_id_neg')->comment('身份证（国徽面）')->change();
            $table->text('pic_edu_background')->comment('学历证书 ')->change();
            $table->text('pic_degree')->comment('学位证书')->change();
            $table->text('pic_pre_company')->comment('前公司离职证明')->change();
            $table->text('pic_user')->comment('员工照片 ')->change();
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
