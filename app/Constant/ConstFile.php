<?php

namespace App\Constant;

use phpDocumentor\Reflection\Types\Self_;

class ConstFile
{
    /*
     * API 常用提示信息
     */
    const API_RESPONSE_SUCCESS = 200;
    const API_RESPONSE_FAIL = -1;
    const API_PARAM_ERROR = 1000001;
    const API_RESPONSE_SUCCESS_MESSAGE = '操作成功';
    const API_RESPONSE_FAIL_MESSAGE = '操作失败';
    const API_DELETE_RECORDS_NOT_EXIST_MESSAGE = '删除记录不存在';
    const API_DELETE_SUCCESS = '删除成功';
    const API_RESPONSE_SUCCESS_MESSAGE_DATA = '获取成功';
    const API_RECORDS_NOT_EXIST_MESSAGE = '记录不存在';
    const API_PARAMETER_MISS = '缺少参数';

    //状态值
    const API_STATUS_NORMAL = 1;
    const API_STATUS_INVALID = 2;
    const API_STATUS_NORMAL_MESSAGE = '正常';
    const API_STATUS_INVALID_MESSAGE = '失效';

    /*
     * schedule 常用类型参数
     */
    //是否全天
    const SCHEDULE_ALL_DAY_YES = 1;
    const SCHEDULE_ALL_DAY_NO = 2;
    public static $scheduleAllDayList = [
        self::SCHEDULE_ALL_DAY_YES => '全天',
        self::SCHEDULE_ALL_DAY_NO => '非全天',
    ];

    //发送方式
    const SCHEDULE_SEND_TYPE_APP = 1;
    const SCHEDULE_SEND_TYPE_SMS = 2;
    public static $scheduleSendTypeList = [
        self::SCHEDULE_SEND_TYPE_APP => '应用',
        self::SCHEDULE_SEND_TYPE_SMS => '短信',
    ];

    //是否重复
    const SCHEDULE_STATUS_REPEAT_NO = 0;
    const SCHEDULE_STATUS_REPEAT_ONE = 1;
    const SCHEDULE_STATUS_REPEAT_TWO = 2;
    const SCHEDULE_STATUS_REPEAT_THREE = 3;
    const SCHEDULE_STATUS_REPEAT_FOUR = 4;
    const SCHEDULE_STATUS_REPEAT_FIVE = 5;

    public static $scheduleStatusRepeatList = [
        self::SCHEDULE_STATUS_REPEAT_NO => '不重复',
        self::SCHEDULE_STATUS_REPEAT_ONE => '重复一次',
        self::SCHEDULE_STATUS_REPEAT_TWO => '重复二次',
        self::SCHEDULE_STATUS_REPEAT_THREE => '重复三次',
        self::SCHEDULE_STATUS_REPEAT_FOUR => '重复四次',
        self::SCHEDULE_STATUS_REPEAT_FIVE => '重复五次',
    ];

    //提醒类型
    const SCHEDULE_PROMPT_TYPE_NO = 0;
    const SCHEDULE_PROMPT_TYPE_FIFTEEN_MINUTES = 1;
    const SCHEDULE_PROMPT_TYPE_ONE_HOUR = 2;
    const SCHEDULE_PROMPT_TYPE_THREE_HOUR = 3;
    const SCHEDULE_PROMPT_TYPE_ONE_DAY = 4;
    public static $schedulePromptTypeList = [
        self::SCHEDULE_PROMPT_TYPE_NO => '不提醒',
        self::SCHEDULE_PROMPT_TYPE_FIFTEEN_MINUTES => '截止前15分钟',
        self::SCHEDULE_PROMPT_TYPE_ONE_HOUR => '截止前1小时',
        self::SCHEDULE_PROMPT_TYPE_THREE_HOUR => '截止前3小时',
        self::SCHEDULE_PROMPT_TYPE_ONE_DAY => '截止前1天',
    ];

    //日程确认状态
    const SCHEDULE_STATUS_CONFIRM_NO = 1;
    const SCHEDULE_STATUS_CONFIRM_YES = 2;
    const SCHEDULE_STATUS_CONFIRM_REJECT = 3;

    public static $scheduleStatusList = [
        self::SCHEDULE_STATUS_CONFIRM_NO => '未接受',
        self::SCHEDULE_STATUS_CONFIRM_YES => '接受',
        self::SCHEDULE_STATUS_CONFIRM_REJECT => '拒绝',
    ];

    /**
     *   考勤
     **/

    const ATTENDANCE_SYTTEM_FIXED = 1;  //规定制

    const ATTENDANCE_SYTTEM_SORT = 2;  //排班制
    const ATTENDANCE_SYTTEM_SORT_CYCLE_ONE = 1; //做一休一
    const ATTENDANCE_SYTTEM_SORT_CYCLE_TWO = 2; //两班轮回
    const ATTENDANCE_SYTTEM_SORT_CYCLE_THR = 3; //三班倒

    const ATTENDANCE_SYTTEM_FREE = 3;  //自由制

    const ATTENDANCE_STAFF_TRUE = 1;    //参加考勤
    const ATTENDANCE_STAFF_FALSE = 2;   //不参加考勤

    const ATTENDANCE_CLASSES_SIESTA = 1;   //午休开启

    const ATTENDANCE_CLASSES_ONE = 1;   //一天 一次上下班
    const ATTENDANCE_CLASSES_TWO = 2;   //一天 两次上下班
    const ATTENDANCE_CLASSES_THR = 3;   //一天 三次上下班

    /**
     *   加班默认规则
     */
    public static $overtime_rule = [
        'is_working_overtime' => 1,
        'working_overtime_type' => 1,
        'working_begin_time' => 30,
        'working_min_overtime' => 60,
        'is_rest_overtime' => 1,
        'rest_overtime_type' => 1,
        'rest_min_overtime' => 60,
    ];
    /*
     * clock 考勤打卡常用类型参数
     */
    const CLOCK_ANOMALY_LATE = 1;     //迟到
    const CLOCK_ANOMALY_LEAVE_EARLY = 2;  //早退
    const CLOCK_ANOMALY_ADDWORK = 3;   //正常

    const CLOCK_START_WORK = 1;   //上班打卡
    const CLOCK_OVER_WORK = 2;    //下班打卡

    const CLOCK_ADDRESS_TYPE_COMPANY = 1;   //公司打卡
    const CLOCK_ADDRESS_TYPE_GETOUT = 2;   //外勤打卡
    const CLOCK_ADDRESS_TYPE_TRIP = 3;   //出差打卡

    const CLOCK_OVERTIME_ISCOUNT_YES = 1;   //统计过加班时间

    const CLOCK_CHECK_STATUS_ING = 1;    //补卡审核中
    const CLOCK_CHECK_STATUS_YES = 2;    //补卡审核通过
    const CLOCK_CHECK_STATUS_NO = 3;    //补卡审核不通过
    public static $clockStatusList = [
        //self::CLOCK_ANOMALY_NORMAL => '正常',
        self::CLOCK_ANOMALY_LATE => '迟到',
        self::CLOCK_ANOMALY_LEAVE_EARLY => '早退',
        self::CLOCK_START_WORK => '上班',
        self::CLOCK_OVER_WORK => '下班',
        self::CLOCK_CHECK_STATUS_ING => '补卡审核中',
        self::CLOCK_CHECK_STATUS_YES => '补卡审核通过',
        self::CLOCK_CHECK_STATUS_NO => '补卡审核不通过',
    ];
    /**
     *   用于统计 临时 请假类别
     */
    const CLOCK_VACATION_NJ = 1;    //年假
    const CLOCK_VACATION_TX = 4;    //调休
    const CLOCK_LEAVEOUT_STATUS_MORMAL = 3; //出勤审核通过

    /**
     * 薪资常用类型参数
     */
    const SALARY_FIXED = 1;
    const SALARY_TRIAL = 2;
    const SALARY_MEAL_SUPPLEMENT = 3;
    const SALARY_TRAFFIC_SUBSIDY = 4;
    const SALARY_RENT_SUBSIDY = 5;
    const SALARY_ENDOWMENT_INSURANCE = 6;
    const SALARY_MEDICAL_INSURANCE = 7;
    const SALARY_UNEMPLOYMENT_INSURANCE = 8;
    const SALARY_EMPLOYMENT_INJURY_INSURANCE = 9;
    const SALARY_MATERNITY_INSURANCE = 10;
    const SALARY_PUBLIC_HOUSING_FUNDS = 11;
    const SALARY_OTHER = 12;
    public static $salaryStatusList = [
        self::SALARY_FIXED => '固定薪资',
        self::SALARY_TRIAL => '试用薪资',
        self::SALARY_MEAL_SUPPLEMENT => '餐补',
        self::SALARY_TRAFFIC_SUBSIDY => '交通补',
        self::SALARY_RENT_SUBSIDY => '租房补',
        self::SALARY_ENDOWMENT_INSURANCE => '养老保险',
        self::SALARY_MEDICAL_INSURANCE => '医疗保险',
        self::SALARY_UNEMPLOYMENT_INSURANCE => '失业保险',
        self::SALARY_EMPLOYMENT_INJURY_INSURANCE => '工伤保险',
        self::SALARY_MATERNITY_INSURANCE => '生育保险',
        self::SALARY_PUBLIC_HOUSING_FUNDS => '住房公积金',
        self::SALARY_OTHER => '其他'
    ];

    /**
     * 入职信息:常用参数
     */
    const ENTRY_STATUS_SEX_MALE = 1;
    const ENTRY_STATUS_SEX_FEMALE = 2;
    public static $entryStatusSexList = [
        self::ENTRY_STATUS_SEX_MALE => '男',
        self::ENTRY_STATUS_SEX_FEMALE => '女',
    ];

    const ENTRY_DEPARTMENT_PRODUCTION = 1;
    const ENTRY_DEPARTMENT_ADVERTISE = 2;
    const ENTRY_DEPARTMENT_PRODUCT = 3;
    const ENTRY_DEPARTMENT_PURCHASE = 4;
    const ENTRY_DEPARTMENT_FINANCE = 5;
    const ENTRY_DEPARTMENT_DESIGN = 6;
    const ENTRY_DEPARTMENT_ON_SALE = 7;
    const ENTRY_DEPARTMENT_QUALITY_CONTROL = 8;
    const ENTRY_DEPARTMENT_STORAGE = 9;
    const ENTRY_DEPARTMENT_MARKET = 10;
    public static $entryDepartmentList = [
        self::ENTRY_DEPARTMENT_PRODUCTION => '产品部',
        self::ENTRY_DEPARTMENT_ADVERTISE => '广告部',
        self::ENTRY_DEPARTMENT_PRODUCT => '生产部',
        self::ENTRY_DEPARTMENT_PURCHASE => '采购部',
        self::ENTRY_DEPARTMENT_FINANCE => '财务部',
        self::ENTRY_DEPARTMENT_DESIGN => '设计部',
        self::ENTRY_DEPARTMENT_ON_SALE => '营业部',
        self::ENTRY_DEPARTMENT_QUALITY_CONTROL => '质检部',
        self::ENTRY_DEPARTMENT_STORAGE => '仓储部',
        self::ENTRY_DEPARTMENT_MARKET => '销售部',
    ];

    const CERTIFICATE_TYPE_IDENTITY = 1;
    const CERTIFICATE_TYPE_CHINESE_PASSPORT = 2;
    const CERTIFICATE_TYPE_GANGAO_PASS_PERMIT = 3;
    const CERTIFICATE_TYPE_GANGAO_LIVE_PERMIT = 4;
    const CERTIFICATE_TYPE_TAIWAN_PASS_PERMIT = 5;
    const CERTIFICATE_TYPE_TAIWAN_LIVE_PERMIT = 6;
    const CERTIFICATE_TYPE_FOREIGN_PASSPORT = 7;
    const CERTIFICATE_TYPE_FORRIGN_WORK_PERMIT_A = 8;
    const CERTIFICATE_TYPE_FORRIGN_WORK_PERMIT_B = 9;
    const CERTIFICATE_TYPE_FORRIGN_WORK_PERMIT_C = 10;
    const CERTIFICATE_TYPE_OTHER = 11;
    public static $certificateTypeList = [
        self::CERTIFICATE_TYPE_IDENTITY => '居民身份证',
        self::CERTIFICATE_TYPE_CHINESE_PASSPORT => '中国护照',
        self::CERTIFICATE_TYPE_GANGAO_PASS_PERMIT => '港澳居民来往内地通行证',
        self::CERTIFICATE_TYPE_GANGAO_LIVE_PERMIT => '港澳居民居住证',
        self::CERTIFICATE_TYPE_TAIWAN_PASS_PERMIT => '台湾居民来往大路通行证',
        self::CERTIFICATE_TYPE_TAIWAN_LIVE_PERMIT => '台湾居民居住证',
        self::CERTIFICATE_TYPE_FOREIGN_PASSPORT => '外国护照',
        self::CERTIFICATE_TYPE_FORRIGN_WORK_PERMIT_A => '外国人工作许可证(A类)',
        self::CERTIFICATE_TYPE_FORRIGN_WORK_PERMIT_B => '外国人工作许可证(B类)',
        self::CERTIFICATE_TYPE_FORRIGN_WORK_PERMIT_C => '外国人工作许可证(C类)',
        self::CERTIFICATE_TYPE_OTHER => '其他个人证件',
    ];

    const STAFF_TYPE_FULL_TIME = 1;
    const STAFF_TYPE_PART_TIME = 2;
    const STAFF_TYPE_LABOR = 3;
    const STAFF_TYPE_OUT_SOURCE = 4;
    const STAFF_TYPE_REHIRE = 5;
    public static $staffTypeList = [
        self::STAFF_TYPE_FULL_TIME => '全职',
        self::STAFF_TYPE_PART_TIME => '兼职',
        self::STAFF_TYPE_LABOR => '劳务派遣',
        self::STAFF_TYPE_OUT_SOURCE => '劳务外包',
        self::STAFF_TYPE_REHIRE => '退休返聘',
    ];

    /*加班申请状态*/
    const ADDWORK = 2; // 未审批
    const ADDWORK_YES = 3; // 已同意
    const ADDWORK_NO = 4; // 已拒绝

    /*分页大小*/
    const PAGE_SIZE = 30;
    /**
     * @deprecated 合同试用期
     */
    const CONTRACT_PROBATION_ONE = 1;// 1，无试用期
    const CONTRACT_PROBATION_TWO = 2;// 2，一个月
    const CONTRACT_PROBATION_THR = 3;// 3，三个月
    public static $probation = [
        self::CONTRACT_PROBATION_ONE => '无试用期',
        self::CONTRACT_PROBATION_TWO => '一个月',
        self::CONTRACT_PROBATION_THR => '三个月',
    ];
    public static $contractMonths = [
        self::CONTRACT_PROBATION_ONE => 0,
        self::CONTRACT_PROBATION_TWO => 1,
        self::CONTRACT_PROBATION_THR => 3,
    ];
    /**
     * @deprecated 合同期限
     */
    const CONTRACT_PERIOD_ONE = 1;// 1，一年
    const CONTRACT_PERIOD_THR = 3;// 3，三年
    const CONTRACT_PERIOD_FIV = 5;// 5，五年
    public static $contract = [
        self::CONTRACT_PERIOD_ONE => '一年',
        self::CONTRACT_PERIOD_THR => '三年',
        self::CONTRACT_PERIOD_FIV => '五年',
    ];

    /**
     * @deprecated 合同审批
     */
    const TOTAL_AUDIT_TYPE_CONTRACT = 5;
    public static $tatalAuditType = [
        self::TOTAL_AUDIT_TYPE_CONTRACT => '合同'
    ];

    /**
     * @deprecated 合同审批状态
     */
    const CONTRACT_STATUS_ONE = 1;
    const CONTRACT_STATUS_TWO = 2;
    const CONTRACT_STATUS_THR = 3;
    public static $contractStatusMsg = [
        self::CONTRACT_STATUS_ONE => '未审核',
        self::CONTRACT_STATUS_TWO => '已审批',
        self::CONTRACT_STATUS_THR => '已拒绝',
    ];
    /**
     * @deprecated 合同状态
     */
    const CONTRACT_STATE_PROBATION_PERIOD = 1;
    const CONTRACT_STATE_TURN_POSIVIVE = 2;
    public static $contractState = [
        self::CONTRACT_STATE_PROBATION_PERIOD => '试用期',
        self::CONTRACT_STATE_TURN_POSIVIVE => '已转正',
    ];

    /**
     * @deprecated 审批状态
     */
    const TOTAL_AUDIT_STATUS_MINUS = -1;
    const TOTAL_AUDIT_STATUS_ONE = 1;
    public static $totalAuditStatusMsg = [
        self::TOTAL_AUDIT_STATUS_MINUS => '拒绝',
        self::TOTAL_AUDIT_STATUS_ONE => '同意',
    ];


    /*
     * 工作汇报，模板字段类型
     * */
    const REPORT_TEMPLATE_FIELD_TYPE_TEXT = 1;
    const REPORT_TEMPLATE_FIELD_TYPE_NUMBER = 2;
    public static $reportTemplateFieldType = [
        self::REPORT_TEMPLATE_FIELD_TYPE_TEXT => ['文本型', '选择此项可填写文字内容', 'text'],
        self::REPORT_TEMPLATE_FIELD_TYPE_NUMBER => ['数字型', '选择此项只能填写数字', 'number']
    ];

    
    const ADD_WORK_COMPANY_TYPE_TRIP = 1;
    const ADD_WORK_COMPANY_TYPE_SALARY = 2;
    public static $addWorkCompanyType = [
        self::ADD_WORK_COMPANY_TYPE_TRIP => '出差',
        self::ADD_WORK_COMPANY_TYPE_SALARY => '薪资',
    ];

    const WORKFLOW_USER_SYNC_STATUS_PENDING_ENTRY = 1;//待入职
    const WORKFLOW_USER_SYNC_STATUS_PENDING_CONTRACT = 2;//待合同
    const WORKFLOW_USER_SYNC_STATUS_WAITING_TO_RUEN_POSITIVE = 3;//待转正
    const WORKFLOW_USER_SYNC_STATUS_PAY_PACKAGE = 4;//工资包
    const WORKFLOW_USER_SYNC_STATUS_LEAVING_OFFICE = 5;//待离职
    const WORKFLOW_USER_SYNC_STATUS_CONTRACT_EXPIRED = 6;//合同到期
    const WORKFLOW_USER_SYNC_STATUS_FIRED = 11;//员工被开除
    const WORKFLOW_USER_SYNC_STATUS_ACTIVE_LEAVE = 12;//员工主动离职
    const WORKFLOW_USER_SYNC_STATUS_ACTIVE_UNDER_HAND_OVER = 13;//离职待交接
    const WORKFLOW_USER_SYNC_STATUS_ACTIVE_LEAVE_UNDER_CONFIRM_LEAVE = 14;//离职待最终确认

    public static $workflowUserSyncStatus = [
        self::WORKFLOW_USER_SYNC_STATUS_PENDING_ENTRY => '待入职',
        self::WORKFLOW_USER_SYNC_STATUS_PENDING_CONTRACT => '合同代签',
        self::WORKFLOW_USER_SYNC_STATUS_WAITING_TO_RUEN_POSITIVE => '待转正',
        self::WORKFLOW_USER_SYNC_STATUS_PAY_PACKAGE => '工资包',
        self::WORKFLOW_USER_SYNC_STATUS_LEAVING_OFFICE => '待离职',
        self::WORKFLOW_USER_SYNC_STATUS_CONTRACT_EXPIRED => '合同到期',
        self::WORKFLOW_USER_SYNC_STATUS_FIRED => '员工被开除',
        self::WORKFLOW_USER_SYNC_STATUS_ACTIVE_LEAVE => '员工主动离职',
        self::WORKFLOW_USER_SYNC_STATUS_ACTIVE_UNDER_HAND_OVER => '离职待交接',
        self::WORKFLOW_USER_SYNC_STATUS_ACTIVE_LEAVE_UNDER_CONFIRM_LEAVE => '交接完待确认',
    ];

    public static $workflowUserSyncStatusMsg = [
        self::WORKFLOW_USER_SYNC_STATUS_PENDING_ENTRY => '待入职',
        self::WORKFLOW_USER_SYNC_STATUS_PENDING_CONTRACT => '合同代签',
        self::WORKFLOW_USER_SYNC_STATUS_WAITING_TO_RUEN_POSITIVE => '待转正',
        self::WORKFLOW_USER_SYNC_STATUS_PAY_PACKAGE => '工资包',
        self::WORKFLOW_USER_SYNC_STATUS_ACTIVE_LEAVE => '待离职',
        self::WORKFLOW_USER_SYNC_STATUS_CONTRACT_EXPIRED => '合同到期',
        self::WORKFLOW_USER_SYNC_STATUS_ACTIVE_UNDER_HAND_OVER => '待交接',
        self::WORKFLOW_USER_SYNC_STATUS_ACTIVE_LEAVE_UNDER_CONFIRM_LEAVE => '待确认',
    ];
    const ADMINISTRATIVE_CONTRACT_YES = 1;//通过
    const ADMINISTRATIVE_CONTRACT_NO = -1;//不通过

    const SALARY_ENTRY_INTO_FORCE_TIME = '2019-1-1 00:00:00';//薪资生效时间


    /**
     * @deprecated 任务状态
     */
    const TASK_STATUS_HAS_NOT_STARTED = 1;//未开始
    const TASK_STATUS_PROCESSING = 2;//处理中
    const TASK_STATUS_COMPLETED = 3;//已完成
    const TASK_STATUS_COMMENTED = 4;//已评价
    const TASK_STATUS_TIMED_OUT_COMMENT = 5;//超时评价
    const TASK_STATUS_TIMED_OUT_HANDLE = 6;//超时完成
    public static $taskStatus = [
        self::TASK_STATUS_HAS_NOT_STARTED => '未开始',
        self::TASK_STATUS_PROCESSING => '处理中',
        self::TASK_STATUS_COMPLETED => '已完成',
        self::TASK_STATUS_COMMENTED => '已评价',
        self::TASK_STATUS_TIMED_OUT_COMMENT => '超时评价',
        self::TASK_STATUS_TIMED_OUT_HANDLE => '超时完成'
    ];





    /*
     * 商品状态
     * */
    const GOODS_STATUS_DRAFT = 0;
    const GOODS_STATUS_ON_SALE = 1;
    const GOODS_STATUS_OBTAINED = 2;
    public static $goods_status = [
        self::GOODS_STATUS_DRAFT => '草稿',
        self::GOODS_STATUS_ON_SALE => '上架',
        self::GOODS_STATUS_OBTAINED => '下架'
    ];

    /*
     * 商品类型
     * */
    const GOODS_TYPE_GOODS = 1;//商品
    const GOODS_TYPE_SERVICE = 2;//服务
    public static $goods_type = [
        self::GOODS_TYPE_GOODS => '商品',
        self::GOODS_TYPE_SERVICE => '服务'
    ];

    /*
     * 商品来源
     * */
    const GOODS_FROM_INTERNAL = 1;//内部商品
    const GOODS_FROM_EXTERNAL = 2;//外部商品
    public static $goods_from = [
        self::GOODS_FROM_INTERNAL => '内部商品',
        self::GOODS_FROM_EXTERNAL => '外部商品'
    ];

    /*
     * 关联工作类型
     * */
    const RELATED_WORK_TYPE_CLIENT = 1;//关联客户
    const RELATED_WORK_TYPE_PROJECT = 2;//关联项目
    const RELATED_WORK_TYPE_PRODUCE = 3;//关联生产
    public static $related_work_type = [
        self::RELATED_WORK_TYPE_CLIENT => '关联客户',
        self::RELATED_WORK_TYPE_PROJECT => '关联项目',
        self::RELATED_WORK_TYPE_PRODUCE => '关联生产'
    ];

    /*
     * 物流信息
     * */
    const SHUNFENG_SHIPPING = 1;//顺丰
    const YUNDA_SHIPPING = 2;//韵达
    const YOUZHENG_SHIPPING = 3;//邮政
    public static $shippingCode = [
        self::SHUNFENG_SHIPPING => '顺丰',
        self::YUNDA_SHIPPING => '韵达',
        self::YOUZHENG_SHIPPING => '邮政'
    ];

    /**
     * 短信
     */
    const SENDLOGINCODE = 'SMS_7816658';//登录
    const SENDREGISTERCODE = 'SMS_7816656';//注册
    const SENDCHANGEPWDCODE = 'SMS_7816654';//修改密码
    const SENDAUTHENTICATIONCODE = 'SMS_7816660';//身份验证
    const SENDINFOSEND = 'SMS_7816653';//信息变更

    const DAY = 86400;
    const HOUR = 3600;

}
