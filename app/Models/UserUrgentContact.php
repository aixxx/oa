<?php
/**
 * Created by PhpStorm.
 * User: lizhennan
 * Date: 2018/9/3
 * Time: 下午6:38
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use DevFixException;

/**
 * App\Models\UserUrgentContact
 *
 * @property int $id
 * @property int $user_id
 * @property string $relate 和联系人的关系
 * @property string $relate_name 联系人姓名
 * @property string $relate_phone 联系人电话
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @property string|null $deleted_at
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserUrgentContact onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserUrgentContact whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserUrgentContact whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserUrgentContact whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserUrgentContact whereRelate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserUrgentContact whereRelateName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserUrgentContact whereRelatePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserUrgentContact whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserUrgentContact whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserUrgentContact withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserUrgentContact withoutTrashed()
 * @mixin \Eloquent
 */
class UserUrgentContact extends Model
{
    use SoftDeletes;
    protected $datas = ['deleted_at'];
    protected $table = 'user_urgent_contacts';
    public $fillable = [
        'user_id',
        'relate',
        'relate_name',
        'relate_phone',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public static function createUrgentContact($userId, $relate, $relateName, $relatePhone)
    {
        $data['user_id'] = $userId;
        $data['relate'] = encrypt($relate);
        $data['relate_name'] = encrypt($relateName);
        $data['relate_phone'] = encrypt($relatePhone);
        $urgentUser = new UserUrgentContact();
        $urgentUser->fill($data);
        $urgentUser->save();
        return $urgentUser;
    }

    public static function deleteUrgentContact($urgentUser)
    {
        $result = $urgentUser->delete();
        return $result;
    }

    /**
     * 循环
     * @param $userId
     * @param $relate
     * @param $relateName
     * @param $relatePhone
     * @return UserUrgentContact
     */
    public static function createUrgentContacts($userId, $array)
    {
        $urgentUser = new UserUrgentContact();
        if (empty($array['act'])) {
            throw new DevFixException("act参数为空，无法判断");
        }
        if (empty($array['arr'])) {
            throw new DevFixException("act参数为空，无法判断");
        }
        $arr = $array['arr'];
        $user = User::findOrFail($userId);
        $oldUserData = $user->toArray();
        $percent = percent($arr[0]);
        //修改百分比
        if($oldUserData['is_urgent_perfect'] !=$percent){
            DB::table('users')->where(['id' => $userId])->update(['is_urgent_perfect'=>$percent]);
        }
        switch ($array['act']) {
            case 'create':
                unset($array['act']);
                DB::transaction(function () use ($arr, $userId, $urgentUser) {
                    foreach ($arr as $key => $val) {
                        $val['user_id'] = $userId;
                        $val['relate'] = encrypt($val['relate']);
                        $val['relate_name'] = encrypt($val['relate_name']);
                        $val['relate_phone'] = encrypt($val['relate_phone']);
                        $urgentUser->create($val);
                    }
                });
                break;
            case 'edit':
                unset($array['act']);
                DB::transaction(function () use ($arr, $userId, $urgentUser) {
                    foreach ($arr as $key => $val) {
                        $val['user_id'] = $userId;
                        $val['relate'] = encrypt($val['relate']);
                        $val['relate_name'] = encrypt($val['relate_name']);
                        $val['relate_phone'] = encrypt($val['relate_phone']);
                        $urgentUser::where('id',$val['id'])->update($val);
                    }
                });
                break;
            default:
                throw new DevFixException('请填写请求类型（act=create：添加，edit：编辑!');
                break;
        }
    }

    public function getUpdateData($data)
    {

        return $data;
    }
}