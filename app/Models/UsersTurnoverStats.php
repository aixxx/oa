<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UsersTurnoverStats
 *
 * @property int $id
 * @property string|null $stats_date 统计日
 * @property string|null $begin_date 期初日
 * @property string|null $end_date 期末日
 * @property int|null $week 当月第几周
 * @property string|null $department_structure 部门体系
 * @property string|null $first_level 一级部门
 * @property int|null $begin_total 期初人数
 * @property int|null $end_total 期末人数
 * @property int|null $ignore_total 未统计人数(包括兼职、顾问、高管、临时)
 * @property int|null $sh_join 上海入职人数
 * @property int|null $sh_leave 上海离职人数
 * @property int|null $cd_join 成都入职人数
 * @property int|null $cd_leave 成都离职人数
 * @property int|null $sz_join 深圳入职人数
 * @property int|null $sz_leave 深圳离职人数
 * @property int|null $bj_join 北京入职人数
 * @property int|null $bj_leave 北京离职人数
 * @property int|null $px_join 萍乡入职人数
 * @property int|null $px_leave 萍乡离职人数
 * @property int|null $part_time_worker 兼职人数
 * @property int|null $adviser 顾问人数
 * @property int|null $leader 高管人数
 * @property int|null $temporary 临时人数
 * @property int|null $passive_leave 被动离职人数
 * @property int|null $voluntary_leave 自愿离职人数
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersTurnoverStats whereAdviser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersTurnoverStats whereBeginDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersTurnoverStats whereBeginTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersTurnoverStats whereBjJoin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersTurnoverStats whereBjLeave($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersTurnoverStats whereCdJoin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersTurnoverStats whereCdLeave($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersTurnoverStats whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersTurnoverStats whereDepartmentStructure($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersTurnoverStats whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersTurnoverStats whereEndTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersTurnoverStats whereFirstLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersTurnoverStats whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersTurnoverStats whereIgnoreTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersTurnoverStats whereLeader($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersTurnoverStats wherePartTimeWorker($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersTurnoverStats wherePassiveLeave($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersTurnoverStats wherePxJoin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersTurnoverStats wherePxLeave($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersTurnoverStats whereShJoin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersTurnoverStats whereShLeave($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersTurnoverStats whereStatsDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersTurnoverStats whereSzJoin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersTurnoverStats whereSzLeave($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersTurnoverStats whereTemporary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersTurnoverStats whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersTurnoverStats whereVoluntaryLeave($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersTurnoverStats whereWeek($value)
 * @mixin \Eloquent
 */
class UsersTurnoverStats extends Model
{

    protected $table = 'users_turnover_stats';

    public $fillable = [
        'id',
        'stats_date',
        'begin_date',
        'end_date',
        'week',
        'department_structure',
        'first_level',
        'begin_total',
        'end_total',
        'ignore_total',
        'sh_join',
        'sh_leave',
        'cd_join',
        'cd_leave',
        'sz_join',
        'sz_leave',
        'bj_join',
        'bj_leave',
        'px_join',
        'px_leave',
        'part_time_worker',
        'adviser',
        'leader',
        'temporary',
        'passive_leave',
        'voluntary_leave',
        'created_at',
        'updated_at',
    ];

    /**
     * 获取最新统计数据
     *
     * @return string 返回
     */
    static public function getLastPeriodBeginDate()
    {
        $lastdate = \DB::select("SELECT max(begin_date) as begin_date FROM users_turnover_stats");

        return $lastdate ? $lastdate[0]->begin_date : 0;
    }
}
