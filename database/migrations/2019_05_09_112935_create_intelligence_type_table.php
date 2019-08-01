<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIntelligenceTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('intelligence_type', function (Blueprint $table) {
            $table->increments('class_id');
            $table->string('class_name')->nullable()->comment('分类名称');
            $table->timestamps();
            $table->softDeletes();//技术
        });
        DB::statement("ALTER TABLE `intelligence_type` comment '情报分类'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('intelligence_type');
    }
}
