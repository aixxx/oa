<?php
/**
 * Created by PhpStorm.
 * User: lizhennan
 * Date: 2018/9/3
 * Time: 下午6:55
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use DevFixException;

/**
 * App\Models\UserFamily
 *
 * @property int $id
 * @property int $user_id
 * @property string $family_relate 和家人的关系
 * @property string $family_name 家人姓名
 * @property string $family_sex 家人性别 1:男 2：女
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @property string|null $deleted_at
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserFamily onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserFamily whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserFamily whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserFamily whereFamilyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserFamily whereFamilyRelate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserFamily whereFamilySex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserFamily whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserFamily whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserFamily whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserFamily withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserFamily withoutTrashed()
 * @mixin \Eloquent
 */
class UserFamily extends Model
{
    use SoftDeletes;
    protected $table = 'user_family';
    protected $datas = ['deleted_at'];

    public $fillable = [
        'user_id',
        'family_relate',
        'family_name',
        'family_sex',
        'birthday',
        'has_children'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
        'family_relate'
    ];

    public static function createUserFamily($userId, $familyRelate, $familyName, $familySex)
    {
        $data['user_id'] = $userId;
        $data['family_relate'] = encrypt($familyRelate);
        $data['family_name'] = encrypt($familyName);
        $data['family_sex'] = encrypt($familySex);
        $userFamily = new UserFamily();
        $userFamily->fill($data);
        $userFamily->save();
        return $userFamily;
    }

    public static function createUserFamilys($userId, $array)
    {
        $userFamily = new UserFamily();
        if (empty($array['act'])) {
            throw new DevFixException("act参数为空，无法判断");
        }
        $arr = $array['arr'];
        $user = User::findOrFail($userId);
        $oldUserData = $user->toArray();
        $percent = percent($arr[0]);
        //修改百分比
        if ($oldUserData['is_family_perfect'] != $percent) {
            DB::table('users')->where(['id' => $userId])->update(['is_family_perfect' => $percent]);
        }
        switch ($array['act']) {
            case 'create':
                unset($array['act']);
                DB::transaction(function () use ($arr, $userId, $userFamily) {
                    foreach ($arr as $key => $val) {
                        $val['user_id'] = $userId;
                        $val['birthday'] = encrypt($val['birthday']);
                        $val['family_name'] = encrypt($val['family_name']);
                        $val['family_sex'] = encrypt($val['family_sex']);
                        $userFamily->create($val);
                    }
                });
                break;
            case 'edit':
                unset($array['act']);
                DB::transaction(function () use ($arr, $userId, $userFamily) {
                    foreach ($arr as $key => $val) {
                        $val['user_id'] = $userId;
                        $val['birthday'] = encrypt($val['birthday']);
                        $val['family_name'] = encrypt($val['family_name']);
                        $val['family_sex'] = encrypt($val['family_sex']);
                        $userFamily::where('id', $val['id'])->update($val);
                    }
                });
                break;
            default:
                throw new DevFixException('请填写请求类型（act=create：添加，edit：编辑!');
                break;
        }

    }

    public static function deleteUserFamily($userFamily)
    {
        $userFamily->delete();
    }
    
}