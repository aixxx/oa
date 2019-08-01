<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/28
 * Time: 14:49
 */

namespace App\Models\Administrative;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    protected $table = "administrative_contract";

    protected $fillable = [
        'title',
        'contract_number',
        'primary_dept',
        'contract_type',
        'entryId',
        'entry_id',
        'secret_level',
        'urgency',
        'main_dept',
        'copy_dept',
        'content',
        'file_upload',
        'status',
        'process_userId',
        'user_id',
    ];

    protected $hidden = [

    ];

}
