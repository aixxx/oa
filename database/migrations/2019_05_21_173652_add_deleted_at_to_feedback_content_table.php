<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeletedAtToFeedbackContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('feedback_content', function (Blueprint $table) {
            $table->tinyInteger('relation_type')->length(4)->comment('关联类型 1-评分');
            $table->integer('relation_id')->length(11)->comment('关联id');
            $table->timestamp('deleted_at')->nullable()->comment('删除时间');
            $table->index('deleted_at');
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
        });
    }
}
