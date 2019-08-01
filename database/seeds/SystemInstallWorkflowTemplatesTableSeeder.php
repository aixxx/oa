<?php

use Illuminate\Database\Seeder;

class SystemInstallWorkflowTemplatesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('workflow_templates')->delete();
        
        \DB::table('workflow_templates')->insert(array (
            0 => 
            array (
                'id' => 28,
                'template_name' => '报销单',
                'created_at' => '2019-04-09 10:45:13',
                'updated_at' => '2019-04-09 10:45:13',
            ),
            1 => 
            array (
                'id' => 30,
                'template_name' => '入职申请表',
                'created_at' => '2019-04-14 16:30:27',
                'updated_at' => '2019-04-14 16:30:27',
            ),
            2 => 
            array (
                'id' => 31,
                'template_name' => '报销流程模板',
                'created_at' => '2019-04-14 20:03:48',
                'updated_at' => '2019-04-14 20:03:48',
            ),
            3 => 
            array (
                'id' => 32,
                'template_name' => '绩效模板',
                'created_at' => '2019-04-15 10:10:18',
                'updated_at' => '2019-04-15 10:10:18',
            ),
            4 => 
            array (
                'id' => 33,
                'template_name' => '外出',
                'created_at' => '2019-04-15 13:58:21',
                'updated_at' => '2019-04-15 13:58:21',
            ),
            5 => 
            array (
                'id' => 34,
                'template_name' => '转正申请表',
                'created_at' => '2019-04-15 17:26:00',
                'updated_at' => '2019-04-15 17:26:00',
            ),
            6 => 
            array (
                'id' => 35,
                'template_name' => '补卡',
                'created_at' => '2019-04-18 09:51:35',
                'updated_at' => '2019-04-18 09:51:35',
            ),
            7 => 
            array (
                'id' => 36,
                'template_name' => '出差',
                'created_at' => '2019-04-18 09:51:35',
                'updated_at' => '2019-04-18 09:51:35',
            ),
            8 => 
            array (
                'id' => 37,
                'template_name' => '请假',
                'created_at' => '2019-04-18 09:51:35',
                'updated_at' => '2019-04-18 09:51:35',
            ),
            9 => 
            array (
                'id' => 38,
                'template_name' => '加班',
                'created_at' => '2019-04-18 09:51:35',
                'updated_at' => '2019-04-18 09:51:35',
            ),
            10 => 
            array (
                'id' => 39,
                'template_name' => '借款流程模板',
                'created_at' => '2019-04-18 19:29:17',
                'updated_at' => '2019-04-18 19:29:17',
            ),
            11 => 
            array (
                'id' => 40,
                'template_name' => '还款流程模板',
                'created_at' => '2019-04-18 19:29:31',
                'updated_at' => '2019-04-18 19:29:31',
            ),
            12 => 
            array (
                'id' => 41,
                'template_name' => '收款流程模板',
                'created_at' => '2019-04-18 19:29:44',
                'updated_at' => '2019-04-18 19:29:44',
            ),
            13 => 
            array (
                'id' => 42,
                'template_name' => '支付流程模板',
                'created_at' => '2019-04-18 19:29:56',
                'updated_at' => '2019-04-18 19:29:56',
            ),
            14 => 
            array (
                'id' => 43,
                'template_name' => '工资包表',
                'created_at' => '2019-04-19 09:32:05',
                'updated_at' => '2019-04-19 09:32:05',
            ),
            15 => 
            array (
                'id' => 44,
                'template_name' => '入职合同表',
                'created_at' => '2019-04-22 10:20:45',
                'updated_at' => '2019-04-22 10:20:45',
            ),
            16 => 
            array (
                'id' => 45,
                'template_name' => '离职交接单',
                'created_at' => '2019-04-22 10:33:26',
                'updated_at' => '2019-04-22 10:33:26',
            ),
            17 => 
            array (
                'id' => 46,
                'template_name' => '开除员工',
                'created_at' => '2019-04-22 10:33:27',
                'updated_at' => '2019-04-22 10:33:27',
            ),
            18 => 
            array (
                'id' => 47,
                'template_name' => '员工申请离职单',
                'created_at' => '2019-04-22 10:33:28',
                'updated_at' => '2019-04-22 10:33:28',
            ),
            19 => 
            array (
                'id' => 49,
                'template_name' => '公文',
                'created_at' => '2019-04-26 16:32:13',
                'updated_at' => '2019-04-26 16:32:13',
            ),
            20 => 
            array (
                'id' => 51,
                'template_name' => '会议申请模板',
                'created_at' => '2019-04-27 10:15:03',
                'updated_at' => '2019-04-30 10:05:02',
            ),
            21 => 
            array (
                'id' => 52,
                'template_name' => '行政文件公文',
                'created_at' => '2019-04-29 16:32:11',
                'updated_at' => '2019-04-29 16:32:11',
            ),
            22 => 
            array (
                'id' => 53,
                'template_name' => '行政新建车辆',
                'created_at' => '2019-05-07 13:26:58',
                'updated_at' => '2019-05-07 13:26:58',
            ),
            23 => 
            array (
                'id' => 54,
                'template_name' => '进销存采购单',
                'created_at' => '2019-05-08 17:05:06',
                'updated_at' => '2019-05-08 17:05:06',
            ),
            24 => 
            array (
                'id' => 55,
                'template_name' => '行政申请用车',
                'created_at' => '2019-05-08 18:05:20',
                'updated_at' => '2019-05-08 18:05:20',
            ),
            25 => 
            array (
                'id' => 56,
                'template_name' => '福利',
                'created_at' => '2019-05-09 17:57:11',
                'updated_at' => '2019-05-09 17:57:11',
            ),
            26 => 
            array (
                'id' => 57,
                'template_name' => '行政申请派车',
                'created_at' => '2019-05-09 18:23:02',
                'updated_at' => '2019-05-09 18:23:02',
            ),
            27 => 
            array (
                'id' => 58,
                'template_name' => '行政归还车辆',
                'created_at' => '2019-05-09 18:23:08',
                'updated_at' => '2019-05-09 18:23:08',
            ),
            28 => 
            array (
                'id' => 59,
                'template_name' => '情报表',
                'created_at' => '2019-05-10 17:02:23',
                'updated_at' => '2019-05-10 17:02:23',
            ),
            29 => 
            array (
                'id' => 60,
                'template_name' => '进销存退货单',
                'created_at' => '2019-05-13 13:20:08',
                'updated_at' => '2019-05-13 13:20:08',
            ),
            30 => 
            array (
                'id' => 61,
                'template_name' => '进销存销售单模板',
                'created_at' => '2019-05-14 09:56:58',
                'updated_at' => '2019-05-14 09:56:58',
            ),
            31 => 
            array (
                'id' => 62,
                'template_name' => '举报投诉表',
                'created_at' => '2019-05-15 10:53:16',
                'updated_at' => '2019-05-15 10:53:16',
            ),
            32 => 
            array (
                'id' => 63,
                'template_name' => '进销存销售退货单模板',
                'created_at' => '2019-05-17 11:22:33',
                'updated_at' => '2019-05-17 11:22:33',
            ),
            33 => 
            array (
                'id' => 64,
                'template_name' => '公司资产借用',
                'created_at' => '2019-05-31 11:13:55',
                'updated_at' => '2019-05-31 11:13:55',
            ),
            34 => 
            array (
                'id' => 65,
                'template_name' => '公司资产归还',
                'created_at' => '2019-05-31 11:14:07',
                'updated_at' => '2019-05-31 11:14:07',
            ),
            35 => 
            array (
                'id' => 66,
                'template_name' => '公司资产调拨',
                'created_at' => '2019-05-31 11:14:23',
                'updated_at' => '2019-05-31 11:14:23',
            ),
            36 => 
            array (
                'id' => 67,
                'template_name' => '公司资产送修',
                'created_at' => '2019-05-31 11:14:33',
                'updated_at' => '2019-05-31 11:14:33',
            ),
            37 => 
            array (
                'id' => 68,
                'template_name' => '公司资产报废',
                'created_at' => '2019-05-31 11:14:42',
                'updated_at' => '2019-05-31 11:14:42',
            ),
            38 => 
            array (
                'id' => 69,
                'template_name' => '公司资产增值',
                'created_at' => '2019-05-31 11:14:51',
                'updated_at' => '2019-05-31 11:14:51',
            ),
            39 => 
            array (
                'id' => 70,
                'template_name' => '公司资产折旧',
                'created_at' => '2019-05-31 11:15:09',
                'updated_at' => '2019-05-31 11:15:09',
            ),
            40 => 
            array (
                'id' => 71,
                'template_name' => '公司资产领用',
                'created_at' => '2019-05-31 11:15:22',
                'updated_at' => '2019-05-31 11:15:22',
            ),
            41 => 
            array (
                'id' => 72,
                'template_name' => '进销存销售出库单审核',
                'created_at' => '2019-05-31 11:17:56',
                'updated_at' => '2019-05-31 11:17:56',
            ),
            42 => 
            array (
                'id' => 73,
                'template_name' => '行政合同表',
                'created_at' => '2019-05-31 11:18:13',
                'updated_at' => '2019-05-31 11:18:13',
            ),
            43 => 
            array (
                'id' => 74,
                'template_name' => '进销存销售退货入库申请',
                'created_at' => '2019-05-31 11:18:28',
                'updated_at' => '2019-05-31 11:18:28',
            ),
            44 => 
            array (
                'id' => 75,
                'template_name' => '会议室添加',
                'created_at' => '2019-05-31 11:18:38',
                'updated_at' => '2019-05-31 11:18:38',
            ),
        ));
        
        
    }
}