<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterFeedbackContentAddColumnAttatchmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('feedback_content', function (Blueprint $table) {
            //
            $table->string('image', 255)->nullable()->comment('图片附件');
            $table->string('video', 255)->nullable()->comment('视频附件');
            $table->string('audio', 255)->nullable()->comment('音频附件');
            $table->string('other_file', 255)->nullable()->comment('文件附件');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('feedback_content', function (Blueprint $table) {
            //
            $table->dropColumn('image');
            $table->dropColumn('video');
            $table->dropColumn('audio');
            $table->dropColumn('other_file');
        });
    }
}
