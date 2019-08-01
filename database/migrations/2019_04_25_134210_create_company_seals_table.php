<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanySealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_seals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->comment('公司id');
            $table->string('seal_name')->comment('印章名称');
            $table->string('seal_img')->comment('印章图片url');
            $table->integer('upload_user_id')->comment('上传者的用户id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_seals');
    }
}
