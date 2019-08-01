<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class FinancialPic.
 *
 * @package namespace App\Models;
 */
class FinancialPic extends Model
{
    use SoftDeletes;

	protected $table = 'financial_pic';

    const FINANCIALS_PIC_TYPE_PIC = 1;//图片
    const FINANCIALS_PIC_TYPE_FILE= 2;//文件
    const FINANCIALS_PIC_TYPE_BILL= 3;//发票

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'pic_type',
        'pic_url',
        'financial_id'
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

}
