<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EntryTypeTable extends Migration
{
    public function up()
    {
        Schema::create('entry_type', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自动编号');
            $table->unsignedBigInteger('entry_id')->length(20)->comment('申请单id');
            $table->unsignedInteger('type_key')->length(4)->comment('类型key值');
            $table->unsignedBigInteger('type_id_value')->length(20)->comment('类型对应的ID');
            $table->softDeletes();
            $table->timestamps();
            $table->index('entry_id');
            $table->index('type_id_value');
        });
        DB::statement("ALTER TABLE `pas_purchase` comment '申请单和某些实际表的关联关系'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entry_type');
    }
}
