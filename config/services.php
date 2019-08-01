<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => env('SES_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],
	//打卡
	'arr1' => [
        'flow_no' => '',
		'type_id'=>'',
		'flow_id' => '',
		'flow_name'=>'打卡',
		'route_url'=>'CheckingIn',
		'icon_url'=>'#icon-cg4'
    ],
	//人事
	'arr2' => [
        'flow_no' => '',
		'type_id'=>'',
		'flow_id' => '',
		'flow_name'=>'人事',
		'route_url'=>'personnelHomepage',
		'icon_url'=>'#icon-sq4'
    ],
	//会议
	'arr3' => [
        'flow_no' => '',
		'type_id'=>'',
		'flow_id' => '',
		'flow_name'=>'会议',
		'route_url'=>'meeting',
		'icon_url'=>'#icon-sq4'
    ],
	//绩效
	'arr4' => [
        'flow_no' => '',
		'type_id'=>'',
		'flow_id' => '',
		'flow_name'=>'绩效',
		'route_url'=>'performance',
		'icon_url'=>'#icon-sq4'
    ],
	//汇报
	'arr5' => [
        'flow_no' => '',
		'type_id'=>'',
		'flow_id' => '',
		'flow_name'=>'汇报',
		'route_url'=>'report',
		'icon_url'=>'#icon-sq4'
    ],
	//督办
	'arr6' => [
        'flow_no' => '',
		'type_id'=>'',
		'flow_id' => '',
		'flow_name'=>'督办',
		'route_url'=>'ADsupervise',
		'icon_url'=>'#icon-sq4'
    ],
	//反馈
	'arr8' => [
        'flow_no' => '',
		'type_id'=>'',
		'flow_id' => '',
		'flow_name'=>'反馈',
		'route_url'=>'newFeedback',
		'icon_url'=>'#icon-kc1'
    ],
	//我的财务
	'arr7' => [
        'flow_no' => '',
		'type_id'=>'',
		'flow_id' => '',
		'flow_name'=>'财务',
		'route_url'=>'finance',
		'icon_url'=>'#icon-sq4'
    ],


    'stripe' => [
        'model' => App\Models\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ]
];
