<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVacationPatchRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vacation_patch_record', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('uid')->comment('用户id');
            $table->integer('company_id')->comment('公司id');
            $table->integer('entry_id')->comment('工作流申请id');
            $table->timestamp('patch_time')->comment('补卡时间点');
            $table->text('reson')->comment('缺卡原因');
            $table->string('file_upload')->comment('图片');
            $table->integer('patch_type')->comment('补卡类型');
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
        Schema::dropIfExists('vacation_patch_record');
    }
}
