<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocialSecurityRelationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('social_security_relation', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ss_id')->length(11)->default(0)->comment('社保配置ID');
            $table->integer('create_user_id')->length(11)->default(0)->comment('创建者ID');
            $table->string('create_user_name')->length(100)->comment('创建者名称');
            $table->integer('user_id')->length(11)->comment('用户ID');
            $table->string('user_name')->length(11)->comment('用户名称');
            $table->integer('company_id')->length(11)->comment('公司ID');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `social_security_relation` comment '社保公积金参与人'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('social_security_relation');
    }
}
