<?php

namespace App\Models\Assets;

use App\Constant\CorporateAssetsConstant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CorporateAssets extends Model
{
    use SoftDeletes;

    protected $table = 'corporate_assets';

    public $timestamps = true;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'num',
        'attr',
        'cat',
        'source',
        'price',
        'metering',
        'buy_time',
        'nature',
        'depreciation_cycle',
        'depreciation_interval',
        'depreciation_method',
        'location',
        'photo',
        'status',
        'department_id',
        'company_id',
        'depreciation_remaining',
        'depreciation_status',
        'remaining_at'
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    protected $appends = [];

    public function hasManyCorporateAssetsRelation()
    {
        return $this->hasMany(CorporateAssetsRelation::class, 'assets_id', 'id');
    }

    public function hasOneCorporateAssetsSyncUse()
    {
        return $this->hasOne(CorporateAssetsSync::class, 'assets_id', 'id')->where(['type' => CorporateAssetsConstant::ASSETS_RELATION_TYPE_USE, 'status' => CorporateAssetsConstant::ASSETS_STATUS_USING]);
    }

    public function hasOneCorporateAssetsSyncBorrow()
    {
        return $this->hasOne(CorporateAssetsSync::class, 'assets_id', 'id')->where(['type' => CorporateAssetsConstant::ASSETS_RELATION_TYPE_BORROW, 'status' => CorporateAssetsConstant::ASSETS_STATUS_USING]);
    }

    public function hasOneCorporateAssetsSyncReturn()
    {
        return $this->hasOne(CorporateAssetsSync::class, 'assets_id', 'id')->where(['type' => CorporateAssetsConstant::ASSETS_RELATION_TYPE_RETURN, 'status' => CorporateAssetsConstant::ASSETS_STATUS_USING]);
    }

    public function hasOneCorporateAssetsUsing()
    {
        return $this->hasOne(CorporateAssetsSync::class, 'assets_id', 'id')->where(['status' => CorporateAssetsConstant::ASSETS_STATUS_USING]);
    }

    //申请
    public function hasOneCorporateAssetsSyncTransfer()
    {
        return $this->hasOne(CorporateAssetsSync::class, 'assets_id', 'id')->where(['type' => CorporateAssetsConstant::ASSETS_RELATION_TYPE_TRANSFER, 'status' => CorporateAssetsConstant::ASSETS_STATUS_USING]);
    }

    public function hasOneCorporateAssetsSyncRepair()
    {
        return $this->hasOne(CorporateAssetsSync::class, 'assets_id', 'id')->where(['type' => CorporateAssetsConstant::ASSETS_RELATION_TYPE_REPAIR, 'status' => CorporateAssetsConstant::ASSETS_STATUS_REPAIR]);
    }

    public function hasOneCorporateAssetsSyncScrapped()
    {
        return $this->hasOne(CorporateAssetsSync::class, 'assets_id', 'id')->where(['type' => CorporateAssetsConstant::ASSETS_RELATION_TYPE_SCRAPPED, 'status' => CorporateAssetsConstant::ASSETS_STATUS_SCRAPPED]);
    }

    public function hasOneCorporateAssetsSyncValueadded()
    {
        return $this->hasOne(CorporateAssetsSync::class, 'assets_id', 'id')->where(['type' => CorporateAssetsConstant::ASSETS_RELATION_TYPE_VALUEADDED]);
    }

    public function hasOneCorporateAssetsSyncDepreciation()
    {
        return $this->hasOne(CorporateAssetsSync::class, 'assets_id', 'id')->where(['type' => CorporateAssetsConstant::ASSETS_RELATION_TYPE_DEPRECIATION]);
    }

    public static function workflowImport($params)
    {

        if (is_array($params)) {
            //无效数据不入库
            if (!isset($params['id']) ||
                !isset($params['status'])
            ) {
                return false;
            }
            $data['status'] = $params['status'];
            self::whereIn('id', $params['id'])->update($data);
        }
    }

    public function hasOneCorporateAssetsSync()
    {
        return $this->hasOne(CorporateAssetsSync::class, 'assets_id', 'id')->where('status','<>',CorporateAssetsConstant::ASSETS_STATUS_REPAIR)->orderBy('id', SORT_DESC);
    }
}
