<?php

namespace App\Models\Workflow;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class WorkflowUserSync extends Model
{
    use SoftDeletes;
    protected $table = 'workflow_user_sync';

    public $fillable = [
        'apply_user_id',
        'user_id',
        'status',
        'content_json',
        'entry_id',
        'confirm_at'
    ];

    public function hasOneUser(){
        return $this->hasOne(User::class,'id','user_id');
    }

    public function hasOneEntry(){
        return $this->hasOne(Entry::class,'id','entry_id');
    }
}