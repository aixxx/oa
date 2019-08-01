<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018/7/30
 * Time: 15:29
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->method()) {
            // CREATE
            case 'POST':
                {
                    return [
//                        'entry_id'                =>'required',
                        'assets.*.name'           => 'required',
//                        'assets.*.cat_id'         => 'required',
                        'assets.*.purchase_price' => 'required|numeric',
                        'assets.*.purchase_time'  => 'required',
                        'assets.*.procurement_id' => 'required',
                        'assets.*.place_id'       => 'required',
                        'assets.*.stock'          => 'required|integer',
                        'assets.*.existed_sku'    => 'required',
                    ];
                }
            // UPDATE
            case 'PUT':
            case 'PATCH':
                {
                    return [
                        // UPDATE ROLES
                    ];
                }
            case 'GET':
            case 'DELETE':
            default:
                {
                    return [];
                };
        }
    }

    /**
     * 自定义错误信息
     * @return array
     */
    public function messages()
    {
        return [
//            'entry_id.required'                => '采购单必填',
            'assets.*.name.required'           => '名称必填',
//            'assets.*.cat_id.required'         => '类型必填',
            'assets.*.purchase_price.required' => '购置价格必填',
            'assets.*.purchase_price.numeric'  => '购置价格必须为数字',
            'assets.*.purchase_time.required'  => '购置日期必填',
            'assets.*.procurement_id.required' => '采购主体必填',
            'assets.*.place_id.required'       => '存放地点必填',
            'assets.*.stock.required'          => '库存必填',
            'assets.*.stock.integer'           => '库存必须为整数',
            'assets.*.existed_sku.required'    => '资产类型必填',
        ];
    }
}