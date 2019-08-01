<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SalarySocialSecurity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salary_social_security', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('user_id');
            $table->integer('year');
            $table->integer('month');
            $table->decimal('medical_personal',10,2)->default(0.00)->comment('医保:个人');
            $table->decimal('medical_company',10,2)->default(0.00)->comment('医保:企业');

            $table->decimal('unemployment_personal',10,2)->default(0.00)->comment('失业:个人');
            $table->decimal('unemployment_company',10,2)->default(0.00)->comment('失业:企业');

            $table->decimal('aged_provide_personal',10,2)->comment('养老:个人');
            $table->decimal('aged_provide_company',10,2)->comment('养老:企业');

            $table->decimal('social_personal',10,2)->default(0.00)->comment('社保:个人');
            $table->decimal('social_company',10,2)->default(0.00)->comment('社保:企业');

            $table->decimal('fund_personal',10,2)->default(0.00)->comment('公积金:个人');
            $table->decimal('fund_company',10,2)->default(0.00)->comment('公积金:企业');

            $table->decimal('supplement_fund_personal',10,2)->default(0.00)->comment('补充公积金:个人');
            $table->decimal('supplement_fund_company',10,2)->default(0.00)->comment('补充公积金:企业');

            $table->string('remark')->nullable()->comment('备注');
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
        Schema::dropIfExists('salary_social_security');
    }
}
