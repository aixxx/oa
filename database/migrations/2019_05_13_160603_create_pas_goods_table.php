<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<EOF
CREATE TABLE `pas_goods` (
  `goods_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品id',
  `category_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分类id',
  `category_parent_id` varchar(50) DEFAULT NULL COMMENT '商品类目组',
  `goods_sn` varchar(60) DEFAULT '' COMMENT '商品编号',
  `goods_name` varchar(120) NOT NULL DEFAULT '' COMMENT '商品名称',
  `suppliers_id` smallint(5) unsigned DEFAULT NULL COMMENT '供应商ID',
  `store_count` smallint(5) unsigned NOT NULL DEFAULT '10' COMMENT '库存数量',
  `wholesale_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '批发价',
  `price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '售价',
  `cost_price` decimal(10,2) DEFAULT '0.00' COMMENT '商品成本价',
  `description` varchar(255) DEFAULT '' COMMENT '商品描述',
  `thumb_img` varchar(255) DEFAULT NULL COMMENT '商品缩略图',
  `img` varchar(255) DEFAULT '' COMMENT '商品图片',
  `goods_type` tinyint(1) DEFAULT '1' COMMENT '商品类型， 1商品 2服务',
  `goods_from` tinyint(1) DEFAULT '0' COMMENT '商品来源 1内部商品 2外部商品',
  `brand_id` int(11) DEFAULT '0' COMMENT '品牌id',
  `mnemonic` varchar(255) DEFAULT NULL COMMENT '助记符',
  `status` tinyint(1) DEFAULT '1' COMMENT '商品状态 0草稿 1上架 2下架',
  `on_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '商品上架时间',
  `sort` smallint(4) unsigned NOT NULL DEFAULT '50' COMMENT '商品排序',
  `sales_num` int(11) DEFAULT '0' COMMENT '商品销量',
  `barcode_scheme` varchar(50) DEFAULT NULL COMMENT '条码方案',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `department` varchar(255) DEFAULT NULL COMMENT '归属部门',
  `organization` varchar(255) DEFAULT NULL COMMENT '归属组织',
  `relate_work` tinyint(1) DEFAULT '0' COMMENT '关联工作 1关联客户 2关联项目 3关联生产',
  `from_system` tinyint(1) DEFAULT '1' COMMENT '添加数据的系统 1erp 2客户',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`goods_id`),
  KEY `goods_sn` (`goods_sn`),
  KEY `cat_id` (`category_id`),
  KEY `goods_number` (`store_count`),
  KEY `sort_order` (`sort`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='商品表';
EOF;
        DB::statement($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pas_goods');
    }
}
