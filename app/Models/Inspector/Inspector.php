<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/14
 * Time: 15:50
 */

namespace App\Models\Inspector;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inspector extends Model
{
    protected $table = "report_complain";
    use SoftDeletes;


    protected $fillable = [
        'user_id',
        'title',
        'content',
        'img_url',
        'video_url',
        'file_url',
        'audio_url',
        'type',
        'state',
        'entry_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}