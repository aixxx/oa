<?php

namespace App\Models\Document;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use SoftDeletes;

    protected $table = 'document';

    public $timestamps  = false;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'user_id','entry_id','doc_title','status','document_number','primary_dept', 'primary_dept_id','doc_type',
        'secret_level', 'urgency','subject','content', 'main_dept','main_dept_id','copy_dept','copy_dept_id',
        'file_upload','deleted_at','created_at','updated_at','authorized_userId'
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    protected $appends = [];


}
