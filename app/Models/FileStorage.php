<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\FileStorage
 *
 * @mixin \Eloquent
 * @property int $id
 * @property int $user_id 操作员工ID
 * @property string $storage_full_path 实际存储地址
 * @property string $storage_system 存储系统
 * @property string $filehash 文件hash
 * @property string $filename 文件名
 * @property string $mime_type meta_data
 * @property string $source_type 文件来源类型
 * @property string $source 文件来源
 * @property string|null $content 内容
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileStorage whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileStorage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileStorage whereFileHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileStorage whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileStorage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileStorage whereMetaData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileStorage whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileStorage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileStorage whereUserId($value)
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileStorage whereMimeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileStorage whereSourceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileStorage whereStorageFullPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileStorage whereStorageSystem($value)
 * @property string $file_hash 文件hash
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileStorage whereFilehash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileStorage whereFilename($value)
 */
class FileStorage extends Model
{
    public $table = 'file_storage';

    public $fillable = ['filehash', 'user_id'];

    const FILE_SYSTEM  = ['local', 'public'];
    const CLOUD_SYSTEM = ['cosv5'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param $hash
     * @param $user_id
     * @return static
     * @author hurs
     */
    public static function firstOrCreateFile($hash, $user_id)
    {
        return self::create(['filehash' => $hash, 'user_id' => $user_id]);
//        return self::firstOrCreate(['file_hash' => $hash, 'user_id' => $user_id]);
    }

    public static function getUserFile($user_id, $limit = 10)
    {
        return self::where('user_id', $user_id)->orderBy('id', 'desc')->paginate($limit);
    }

    public function getType()
    {
        return explode("/", $this->mime_type)[0];
    }
}
