<?php
namespace App\Models;

use App\Models\Basic\BasicUserRank;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;



class VoteRule extends Model
{
    use SoftDeletes;
    
    protected $table = 'vote_rule';
    
    public $timestamps  = true;
    
    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
    
    protected $appends = [];

    public function getUserRank(){
        return $this->hasOne(BasicUserRank::class,'id','job_grade');
    }



}
