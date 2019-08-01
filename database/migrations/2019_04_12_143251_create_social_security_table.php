<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocialSecurityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('social_security', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->length(10)->comment('公司编号');
            $table->string('name')->length(100)->comment('社保名称');
            $table->string('english_name')->length(100)->comment('英文名称');
            $table->string('proportion')->length(100)->comment('交金比例');
            $table->integer('create_user_id')->length(10)->comment('创建人编号');
            $table->string('create_user_name')->length(100)->comment('创建人名称');
            $table->timestamps();
            $table->softDeletes();
            $table->index('deleted_at');
        });
        DB::statement("ALTER TABLE `social_security` comment '社保配置表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('social_security');
    }
}
