<?php

namespace App\Models\Feedback;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeedbackReply extends Model
{
//    use SoftDeletes;
    
    protected $table = 'feedback_reply';
    
    public $timestamps  = false;
    
    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        
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
