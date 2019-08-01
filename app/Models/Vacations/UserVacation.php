<?php

namespace App\Models\Vacations;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Vacations\Vacation
 *
 * @property int $id
 * @property int $user_id 用户
 * @property int|null $annual_time 年假（分钟）
 * @property int|null $rest_time 调休（分钟）
 * @property int|null $menstrual_time 例假（分钟）
 * @property int|null $maternity_cnt 请过产假次数
 * @property int|null $paternity_cnt 请过陪产假次数
 * @property int|null $marital_cnt 请过婚假次数
 * @property int|null $breastfeeding_cnt 请过哺乳假次数
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vacations\UserVacation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vacations\UserVacation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vacations\UserVacation query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vacations\UserVacation whereAnnualTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vacations\UserVacation whereBreastfeedingCnt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vacations\UserVacation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vacations\UserVacation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vacations\UserVacation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vacations\UserVacation whereMaritalCnt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vacations\UserVacation whereMaternityCnt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vacations\UserVacation whereMenstrualTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vacations\UserVacation wherePaternityCnt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vacations\UserVacation whereRestTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vacations\UserVacation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vacations\UserVacation whereUserId($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vacations\UserVacation decrementAnnualTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vacations\UserVacation decrementMenstrualTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vacations\UserVacation decrementRestTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vacations\UserVacation incrementBreastfeedingCnt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vacations\UserVacation incrementMaritalCnt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vacations\UserVacation incrementMaternityCnt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vacations\UserVacation incrementPaternityCnt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vacations\UserVacation uid($value)
 */
class UserVacation extends Model
{
    //
    protected $table = 'user_vacation';

    protected $fillable = ['user_id', 'annual_time', 'rest_time',
        'menstrual_time', 'maternity_cnt', 'paternity_cnt',
        'marital_cnt', 'breastfeeding_cnt'];

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return mixed
     */
    public function scopeUid($query, $value){
        return $query->where('user_id', '=', $value);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return mixed
     */
    public function scopeDecrementAnnualTime($query, $value){
        return $query->decrement('annual_time', $value);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return mixed
     */
    public function scopeDecrementRestTime($query, $value){
        return $query->decrement('rest_time', $value);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return mixed
     */
    public function scopeDecrementMenstrualTime($query, $value){
        return $query->decrement('menstrual_time', $value);
    }

    /**
     * @param   \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return mixed
     */
    public function scopeIncrementMaternityCnt($query, $value){
        return $query->increment('maternity_cnt');
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return mixed
     */
    public function scopeIncrementPaternityCnt($query, $value){
        return $query->increment('paternity_cnt');
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return mixed
     */
    public function scopeIncrementMaritalCnt($query, $value){
        return $query->increment('marital_cnt');
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return mixed
     */
    public function scopeIncrementBreastfeedingCnt($query, $value){
        return $query->increment('breastfeeding_cnt');
    }
}
