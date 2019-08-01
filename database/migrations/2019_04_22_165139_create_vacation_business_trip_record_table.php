<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVacationBusinessTripRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vacation_business_trip_record', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('uid')->comment('用户id');
            $table->integer('company_id')->comment('公司id');
            $table->integer('entry_id')->comment('工作流申请id');
            $table->text('reson')->comment('出差事由');
            $table->integer('trip_days')->comment('出差天数');
            $table->text('remark')->nullable()->comment('出差备注');
            $table->string('other_people', 200)->nullable()->comment('同行人');
//            $table->integer('trip_id')->comment('行程id');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vacation_business_trip_record');
    }
}
