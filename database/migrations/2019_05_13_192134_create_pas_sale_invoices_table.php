<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasSaleInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<EOF
CREATE TABLE `pas_sale_invoices` (
`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`order_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单信息',
`type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '发货方式 1物流',
`shipping_id` int(10) NOT NULL DEFAULT '0' COMMENT '物流id',
`network_id` int(10) DEFAULT '0' COMMENT '网点id',
`network_mobile` varchar(20) DEFAULT '' COMMENT '网点电话',
`waybill_number` varchar(20) DEFAULT NULL COMMENT '运货单号',
`consignee` varchar(50) DEFAULT NULL COMMENT '收件人',
`mobile` varchar(20) DEFAULT NULL COMMENT '联系电话',
`money` decimal(8,2) DEFAULT '0.00' COMMENT '物流费用',
`remark` varchar(255) DEFAULT NULL,
`province` int(11) NOT NULL DEFAULT '0' COMMENT '省份',
`city` int(11) NOT NULL DEFAULT '0' COMMENT '城市',
`county` int(11) NOT NULL DEFAULT '0' COMMENT '地区',
`twon` int(11) DEFAULT '0' COMMENT '乡镇',
`address` varchar(250) DEFAULT '' COMMENT '地址',
`zipcode` varchar(60) DEFAULT '' COMMENT '邮政编码',
PRIMARY KEY (`id`),
KEY `user_id` (`order_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='销售发货单表';
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
        Schema::dropIfExists('pas_sale_invoices');
    }
}
