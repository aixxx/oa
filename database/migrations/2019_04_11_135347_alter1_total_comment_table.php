<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Alter1TotalCommentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('total_comment', function (Blueprint $table) {
            $table->integer('audit_id')->nullable()->change();
            $table->string('user_name',255)->change();
            $table->string('comment_text',255)->change();
            $table->string('comment_img',255)->nullable()->change();
            $table->string('comment_field',255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addwork');
    }
}
