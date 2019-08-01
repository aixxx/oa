<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Investment extends Model
{
    protected $fillable = ['title', 'id'];
    public $account_type_id = 1;
	
    public function getAccountRecordTitle() {
        return [
		'title'=>$this->title,
		'sub'=>'自定义副标题'
	];
    }
}
