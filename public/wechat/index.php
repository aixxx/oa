<?php
include_once "WXBizMsgCrypt.php";
include_once "sha1.php";
include_once "xmlparse.php";
include_once "pkcs7Encoder.php";
include_once "errorCode.php";

// 假设企业号在公众平台上设置的参数如下
$encodingAesKey = "jWmYm7qr5nMoAUwZRjGtBxmz3KA1tkAj3ykkR6q2B2C";
$token = "QDG6eK";


$sVerifyMsgSig = urldecode($_GET['msg_signature']);
$sVerifyTimeStamp = urldecode($_GET['timestamp']);
$sVerifyNonce = urldecode($_GET['nonce']);
$sVerifyEchoStr = urldecode($_GET['echostr']);

/*$array = array($sVerifyEchoStr, $token, $sVerifyTimeStamp, $sVerifyNonce);
sort($array, SORT_STRING);
$str = implode($array);
$signature = sha1($str) ;*/


if (strlen($encodingAesKey) != 43) {
    return  'encodingAesKey 非法';
}


$sha1 = new SHA1;
$array = $sha1->getSHA1($token, $sVerifyTimeStamp, $sVerifyNonce, $sVerifyEchoStr);
$ret = $array[0];

if ($ret != 0) {
    return $ret;
}
$signature = $array[1];
if ($signature != $sVerifyMsgSig ) {
    return '40001: 签名验证错误';
}

