<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertApiRoutesData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('api_routes', function (Blueprint $table) {
            //
            $sql = "INSERT INTO `api_routes` VALUES 
(default, 'annual_rule.list', '年假规则列表', 0, NULL, NULL, NULL, 1),
(default, 'annual_rule.add', '增加年假规则', 0, NULL, NULL, NULL, 1),
(default, 'annual_rule.delete', '删除年假规则', 0, NULL, NULL, NULL, 1),
(default, 'vacation.leave', '展示请假', 0, NULL, NULL, NULL, 1),
(default, 'vacation.extra.save', '保存加班', 0, NULL, NULL, NULL, 1),
(default, 'vacation.workday', '工作日非工作日', 0, NULL, NULL, NULL, 1),
(default, 'feedback.feed', '反馈列表', 0, NULL, NULL, NULL, 1),
(default, 'vacation.patch.show', '展示补卡', 0, NULL, NULL, NULL, 1)";
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('api_routes', function (Blueprint $table) {
            //
        });
    }
}
