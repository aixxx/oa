<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTripTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trip', function (Blueprint $table) {
            $table->increments('id');
            $table->string('trip_number')->length(255)->comment('出差编号');
            $table->integer('userid')->length(11)->comment('申请人ID');
            $table->string('uname')->length(11)->nullable()->comment('申请人姓名');
            $table->string('dept')->length(100)->nullable()->comment('申请人部门');
            $table->string('position')->length(20)->nullable()->comment('申请人职位');
            $table->text('cause')->nullable()->comment('出差事由');
            $table->string('trip_count')->nullable()->comment('出差天数');
            $table->string('trip_info')->nullable()->comment('出差备注');
            $table->string('together_person')->nullable()->comment('同行人');
            $table->tinyInteger('status')->nullable()->comment('审批状态');
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
        Schema::dropIfExists('trip');
    }
}
