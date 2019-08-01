<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\OSS;
use App\Models\User;
use Request;
use Auth;
use DB;

//use Intervention\Image\Image;

class AliossController extends BaseController
{
    public function upload_oss()
    {
        $input = Request::input('upload_type');
        $userinfo = Auth::user();
        if (in_array($input, [1, 2])) {
            //base64类型
            if ($input == 1) {//base64类型的
                $imgBase64 = Request::input('base_file');
                if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $imgBase64, $res)) {
                    //获取图片类型
                    $type = $res[2];
                    $mimetype = strstr(substr($res[1], 5), ';', TRUE);
                    //图片名字
                    $fileName = md5(time()) . '.' . $type;
                    // 临时文件
                    $tmpfname = tempnam(__DIR__, "TIAN");
                    //保存图片
                    $handle = fopen($tmpfname, "w");
                    //阿里云oss上传的文件目录
                    $dst = date('Y-m/d') . '/' . $fileName;
                    if (fwrite($handle, base64_decode(str_replace($res[1], '', $imgBase64)))) {
                        $resu = OSS::upload($dst, $tmpfname, ['ContentType' => $mimetype]);
                        #关闭缓存
                        fclose($handle);
                        #删除本地该图片
                        unlink($tmpfname);
                        if ($resu) {
                            $url = OSS::getUrl($dst);
                            $insert['uid'] = $userinfo->id;
                            $insert['img_url'] = $url;
                            DB::table('image')->insert($insert);
                            return returnJson($message = '上传成功', $code = '200', $url);
                        } else {
                            return returnJson($message = '上传失败', $code = '1003');
                        }
                    } else {
                        return returnJson($message = '写入失败', $code = '1002');
                    }
                }
            }

            if ($input == 2) {//正常类型
                $file = Request::file('file');
                if (!empty($file)) {
                    //获取上传图片的临时地址
                    $tmppath = $file->getRealPath();
                    //生成文件名
                    $fileName = str_random(5) . time() . date('ymd') . '.' . $file->getClientOriginalExtension();
                    //拼接上传的文件夹路径(按照日期格式1810/17/xxxx.jpg)
                    $pathName = date('Y-m/d') . '/' . $fileName;
                    //上传图片到阿里云OSS
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);         //获取文件的mime类型
                    $mimetype = finfo_file($finfo, $file);
                    $result = OSS::upload($pathName, $tmppath, ['ContentType' => $mimetype]);
                    if ($result) {
                        //获取上传图片的Url链接
                        $url = OSS::getUrl($pathName);
                        $insert['uid'] = $userinfo->id;
                        $insert['img_url'] = $url;
                        DB::table('image')->insert($insert);
                        return returnJson($message = '上传成功', $code = '200', $url);
                    } else {
                        return returnJson($message = '上传失败', $code = '1004');
                    }
                }
            }
        } else {
            return returnJson($message = '上传类型有误', $code = '1001');
        }
    }

    public function upload()
    {
        $file = Request::file('file');
        $userinfo = Auth::user();
        if (!empty($file)) {
            //获取上传图片的临时地址
            $tmppath = $file->getRealPath();
            //生成文件名
            $fileName = str_random(5) . time() . date('ymd') . '.' . $file->getClientOriginalExtension();
            $newPath = $this->getStamp($tmppath, $fileName);
            //拼接上传的文件夹路径(按照日期格式1810/17/xxxx.jpg)
            $pathName = date('Y-m/d') . '/' . $fileName;
            //上传图片到阿里云OSS
            $finfo = finfo_open(FILEINFO_MIME_TYPE);         //获取文件的mime类型
            $mimetype = finfo_file($finfo, $file);
            $result = OSS::upload($pathName, $newPath, ['ContentType' => $mimetype]);
            if ($result) {
                //获取上传图片的Url链接
                $url = OSS::getUrl($pathName);
                $insert['uid'] = $userinfo->id;
                $insert['img_url'] = $url;
                DB::table('image')->insert($insert);
                return returnJson($message = '上传成功', $code = '200', $url);
            } else {
                return returnJson($message = '上传失败', $code = '1004');
            }
        }
    }


    public function getStamp($path = '', $fileName = "")
    {
        $image = file_get_contents($path);
        $info = getimagesize($path);
        $im = imagecreatefromstring($image);
        $width = $info[0];
        $height = $info[1];
        for ($i = 0; $i < $height; $i += 1) {
            for ($j = 0; $j < $width; $j += 1) {
                $rgb = ImageColorAt($im, $j, $i);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                if (intval($r) > 220 && $g > 220 && $b > 220) {
                    $hex = imagecolorallocate($im, 255, 255, 255);
                    imagesetpixel($im, $j, $i, $hex);
                }
            }
        }
        $white = imagecolorallocate($im, 255, 255, 255);//拾取白色
        imagefill($im, 0, 0, $white);//把画布染成白色
        imagecolortransparent($im, $white);//把图片中白色设置为透明色
        imagepng($im, storage_path($fileName));//生成图片
        return storage_path($fileName);
    }
}