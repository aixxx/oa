<?php

namespace App\Models\Addwork;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AddworkCompany extends Model
{
//    use SoftDeletes;
    
    protected $table = 'addwork_company';
    
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

    public function hasOneAddWorkField(){
        $this->hasOne(AddworkField::class);
    }

    public function hasOneAddWorkFieldGet(){
        return $this->hasOne(AddworkField::class,'id','field_id');
    }

}
