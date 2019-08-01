<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/9
 * Time: 13:18
 */

namespace App\Models\Intelligence;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class IntelligenceInfo extends Model
{
    protected $table = "intelligence_info";
    use SoftDeletes;

    protected $fillable = [
        'user_inte_id',
        'inte_content',
        'inte_demand',
        'file_upload',
        'time',
        'bank',
        'reason',
        'auditstate',
    ];

    protected $hidden = [

    ];
}