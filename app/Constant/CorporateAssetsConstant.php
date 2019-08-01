<?php

namespace App\Constant;

/**
 * Class CorporateAssetsConstant
 *
 * @package     App\Constant
 * @description 公共常量类
 */
class CorporateAssetsConstant
{
    CONST ATTR_FIXED_ASSETS = 1;
    CONST ATTR_FICTITIOUS_ASSETS = 2;
    public static $attr = [
        self::ATTR_FIXED_ASSETS => '固定资产',
        self::ATTR_FICTITIOUS_ASSETS => '虚拟资产'
    ];
    //固定资产的分类
    CONST CATEGORY_COMPUTER_EQUIPMENT = 1;
    CONST CATEGORY_OFFICE_EQUIPMENT = 2;
    CONST CATEGORY_COMMUNICATION_EQUIPMENT = 3;
    CONST CATEGORY_FURNITURE_EQUIPMENT = 4;
    CONST CATEGORY_OTHER_FIXED_ASSETS = 5;
    //虚拟资产的分类
    CONST CATEGORY_SOFTWARE_EQUIPMENT = 6;
    CONST CATEGORY_OTHER_FICTITIOUS_ASSETS = 7;
    public static $category = [
        self::CATEGORY_COMPUTER_EQUIPMENT => '计算机设备',
        self::CATEGORY_OFFICE_EQUIPMENT => '办公设备',
        self::CATEGORY_COMMUNICATION_EQUIPMENT => '通信设备',
        self::CATEGORY_FURNITURE_EQUIPMENT => '家具用具',
        self::CATEGORY_OTHER_FIXED_ASSETS => '其他固定资产',
        self::CATEGORY_SOFTWARE_EQUIPMENT => '软件',
        self::CATEGORY_OTHER_FICTITIOUS_ASSETS => '其他虚拟资产',
    ];
    CONST SOURCE_PURCHASE = 1;
    CONST SOURCE_RECEIVING_INVESTMENT = 2;
    CONST SOURCE_TRANSFER_IN = 3;
    CONST SOURCE_SELF_BUILT = 4;
    public static $source = [
        self::SOURCE_PURCHASE => '购入',
        self::SOURCE_RECEIVING_INVESTMENT => '接收投资',
        self::SOURCE_TRANSFER_IN => '调入',
        self::SOURCE_SELF_BUILT => '自建',
    ];
    CONST NATURE_DEPRECIATION_ASSETS = 1;
    CONST NATURE_VALUE_ADDED_ASSETS = 2;
    public static $nature = [
        self::NATURE_DEPRECIATION_ASSETS => '折旧资产',
        self::NATURE_VALUE_ADDED_ASSETS => '增值资产',
    ];

    CONST DEPRECIATION_METHOD_LINEAR = 1;
    CONST DEPRECIATION_METHOD_DECREMENT = 2;
    public static $depreciation_method = [
        self::DEPRECIATION_METHOD_LINEAR => '线性',
        self::DEPRECIATION_METHOD_DECREMENT => '递减'
    ];

    CONST ASSETS_RELATION_TYPE_USE = 1;
    CONST ASSETS_RELATION_TYPE_BORROW = 2;
    CONST ASSETS_RELATION_TYPE_RETURN = 3;
    CONST ASSETS_RELATION_TYPE_TRANSFER = 4;
    CONST ASSETS_RELATION_TYPE_REPAIR = 5;
    CONST ASSETS_RELATION_TYPE_SCRAPPED = 6;
    CONST ASSETS_RELATION_TYPE_VALUEADDED = 7;
    CONST ASSETS_RELATION_TYPE_DEPRECIATION = 8;
    public static $assets_relation_type = [
        self::ASSETS_RELATION_TYPE_USE => '领用',
        self::ASSETS_RELATION_TYPE_BORROW => '借用',
        self::ASSETS_RELATION_TYPE_RETURN => '归还',
        self::ASSETS_RELATION_TYPE_TRANSFER => '调拨',
        self::ASSETS_RELATION_TYPE_REPAIR => '送修',
        self::ASSETS_RELATION_TYPE_SCRAPPED => '报废',
        self::ASSETS_RELATION_TYPE_VALUEADDED => '增值',
        self::ASSETS_RELATION_TYPE_DEPRECIATION => '折旧',
    ];

    CONST ASSETS_STATUS_IDLE = 1;
    CONST ASSETS_STATUS_USING = 2;
    CONST ASSETS_STATUS_TRANSFER = 3;
    CONST ASSETS_STATUS_REPAIR = 4;
    CONST ASSETS_STATUS_SCRAPPED = 5;
    public static $assets_status = [
        self::ASSETS_STATUS_IDLE => '闲置',
        self::ASSETS_STATUS_USING => '在用',
        self::ASSETS_STATUS_TRANSFER => '调拨',
        self::ASSETS_STATUS_REPAIR => '维修',
        self::ASSETS_STATUS_SCRAPPED => '报废'
    ];

    CONST ASSETS_DEPRECIATION_STATUS_CAN = 1;
    CONST ASSETS_DEPRECIATION_STATUS_NO = 2;

}
