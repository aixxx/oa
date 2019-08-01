<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Services\Message\MessageService;

class CertificateController extends Controller
{
    public function download()
    {
        $uid = auth()->user()->id;

        $cert        = new Certificate();
        $certificate = $cert->getCert($uid);

        MessageService::send('download_certificate', [
            'to'       => [
                'email'  => [auth()->user()->email],
                'wechat' => [$certificate->name],
            ],
            'password' => decrypt($certificate->password),
        ]);

        $content = base64_decode($certificate->pkcs12);

        $response = new \Illuminate\Http\Response($content);
        $response->header('Content-Type', 'application/x-pkcs12');
        $response->header('Content-Disposition', 'attachment; filename=' . $certificate->name . '.p12');
        $response->header('Content-Transfer-Encoding', 'binary');

        return $response;
    }

    public function crl()
    {

        $cert     = new Certificate();
        $certText = $cert->getCrl();

        $response = new \Illuminate\Http\Response($certText);
        $response->header('Content-Type', 'application/pem');
        $response->header('Content-Disposition', 'attachment; filename=crl.pem');
        $response->header('Content-Transfer-Encoding', 'binary');

        return $response;
    }
}