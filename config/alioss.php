<?php
return [
    /**
     * 经典网络 or VPC
     */
    'networkType' => env('OSS_NET_WORK_TYPE','经典网络'),
    /**
     * 城市名称：
     *  经典网络下可选：杭州、上海、青岛、北京、张家口、深圳、香港、硅谷、弗吉尼亚、新加坡、悉尼、日本、法兰克福、迪拜
     *  VPC 网络下可选：杭州、上海、青岛、北京、张家口、深圳、硅谷、弗吉尼亚、新加坡、悉尼、日本、法兰克福、迪拜
     */
    'city' => env('OSS_CITY','上海'),
    'useInternal' => env('ALIOSS_USEINTERNAL',false),       //是否使用OSS内网传输来省流量
    'ossServer' => env('ALIOSS_SERVER', 'http://oss-cn-shanghai.aliyuncs.com'),                                // 外网
    'ossServerInternal' => env('ALIOSS_SERVERINTERNAL', 'http://aikeerp.oss-cn-shanghai.aliyuncs.com'),     // 内网
    'AccessKeyId' => env('ALIOSS_KEYID', 'LTAIk0ALGctA12hm'),                                            // key
    'AccessKeySecret' => env('ALIOSS_KEYSECRET', 'mEKKjhslIysh8Llu0rqGMA9LDhtZMn'),                   // secret
    'BucketName' => env('ALIOSS_BUCKETNAME', 'aikeerp')                                                  // bucket
];