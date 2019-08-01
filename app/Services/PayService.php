<?php
namespace App\Services;

use App\Http\Helpers\Signature;
use GuzzleHttp\Client;
use DevFixException;
/**
 * Created by PhpStorm.
 * User: aike
 * Date: 2018/7/24
 * Time: 下午10:36
 */

class PayService
{
    const PAY_STATUS_SUCCESS    = 0; // 成功
    const PAY_STATUS_FAIL       = 1; // 失败
    const PAY_STATUS_PROCESSING = 2; // 处理中

    const METHOD_POST = 'POST';
    const METHOD_GET  = 'GET';

    const PAY_URL = '/pay/withdraw';

    private $baseUri;

    public function __construct()
    {
        $this->baseUri = config('capital.base_url');
    }

    public function pay($payParams)
    {
        $client = new Client([
            'base_uri' => $this->baseUri,
        ]);

        $signKey = config('capital.sign_key');
        $signature = new Signature($signKey);
        $sign = $signature->sign($payParams);
        $payParams['sign'] = $sign;

        $response = $client->request(self::METHOD_POST, self::PAY_URL, [
            'connect_timeout' => 5, // 超时时间5秒钟
            'body'            => json_encode($payParams)
        ]);

        $body = (string)$response->getBody();

        $bodyDecoded = json_decode($body, true);

        // 如果不包含code,或者(不是成功状态,并且不是处理中状态),则抛异常,需要任务重试
        if (!isset($bodyDecoded['code']) ||
            ($bodyDecoded['code'] !== self::PAY_STATUS_SUCCESS && $bodyDecoded['code'] !== self::PAY_STATUS_PROCESSING)) {
            throw new DevFixException($body);
        }

        return $bodyDecoded;
    }
}
