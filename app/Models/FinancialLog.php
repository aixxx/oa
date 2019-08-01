<?php

namespace App\Models;

use App\Models\Workflow\Entry;
use App\Models\Workflow\Flow;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Financial.
 *
 * @package namespace App\Models;
 */
class FinancialLog extends Model
{
	use SoftDeletes;

	protected $table = 'financial_log';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'financial_id',
        'operator',
        'status',
        'user_id',
        'remarks'
	];
	/**
     * 不能被批量赋值的属性
     *
     * @var array
     */
    protected $guarded = [
        "id"
    ];
    protected $hidden = [
        'deleted_at'
    ];

    public function finance(){
       return $this->hasOne(FinancialLog::class,'id','financial_id');
    }
    public  function operator(){
        return $this->hasOne(User::class,'id', 'operator')->select('id','chinese_name');
    }
    public static function insertFinanceLog($data){
        $rt=FinancialLog::create($data);
        return $rt;
    }

}
