<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\TagUser
 *
 * @property int $id
 * @property int $tag_id
 * @property int $user_id
 * @property string $user_name
 * @property-read \App\Models\Tag $tag
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TagUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TagUser whereTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TagUser whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TagUser whereUserName($value)
 * @mixin \Eloquent
 */
class TagUser extends Model
{
    protected $table = 'tag_user';
    protected $fillable = ['id','tag_id', 'user_id', 'user_name'];

    public function tag()
    {
        return $this->hasOne('App\Models\Tag','id','tag_id');
    }

    public function user()
    {
        return $this->hasOne('App\Models\User','id','user_id');
    }

    /**
     * 创建记录
     *
     * @param $companyId int 公司编号
     * @param $userId   int  员工编号
     * @param $name     string  企业唯一name
     *
     * @return bool
     */
    public static function saveTagUser($companyId, $userId, $name)
    {
        $company = Company::findOrFail($companyId);
        $tagName = Tag::TAG_NAME_COMPANY . $company->name;
        $tag = Tag::where('name', '=', $tagName)->first();
        if(!$tag){
            $tag=Tag::create(['name'=>$tagName]);
        }

        $tagUser = self::where('tag_id', '=', $tag->id)->where('user_id', '=', $userId)->first();

        if (empty($tagUser)) {
            $insert['tag_id']    = $tag->id;
            $insert['user_id']   = $userId;
            $insert['user_name'] = $name;

            return DB::table('tag_user')->insert($insert);
        }

        return false;
    }

    /**
     * 删除记录
     *
     * @param $companyId int 公司编号
     * @param $userId   int  员工编号
     * @param $name     string  企业唯一name
     *
     * @return bool
     */
    public static function deleteTagUserByUserId($userId)
    {
        $tagUser = self::where('user_id', '=', $userId)->get()->toArray();

        if (count($tagUser)) {
            return DB::table('tag_user')->where('user_id', '=', $userId)->delete();
        }

        return true;
    }


    public static function getTagByCompanyId($companyId)
    {
        $company = Company::findOrFail($companyId);
        $tagName = Tag::TAG_NAME_COMPANY . $company->name;
        return Tag::where('name', '=', $tagName)->first();
    }
}