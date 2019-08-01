<?php

namespace App\Services\Common;

use UserFixException;

class FileService
{
    public $fileName = null;
    public $content  = null;
    public $type     = null;

    const TYPE_JSON  = 'json';
    const TYPE_EXCEL = 'excel';
    public static $typeList = [
        self::TYPE_EXCEL => 'excel',
        self::TYPE_JSON  => 'json',
    ];

    public function __construct($type, $fileName, $content)
    {
        $this->type     = $type;
        $this->fileName = $fileName;
        $this->content  = $content;
    }

    public function export()
    {
        $this->check();

        if (self::TYPE_JSON == $this->type) {
            $this->exportJson();
        }
    }

    private function check()
    {
        if (empty($this->type)) {
            throw new UserFixException('类型不能为空');
        }

        if (empty($this->fileName)) {
            throw new UserFixException('文件名字不能为空');
        }

        if (empty($this->content)) {
            throw new UserFixException('文件内容不能为空');
        }

        if (!in_array($this->type, self::$typeList)) {
            throw new UserFixException('类型不存在');
        }
    }

    private function exportJson()
    {
        header('Content-Type: application/json');
        header('Content-Disposition: attachment;filename=' . $this->fileName);//告诉浏览器输出浏览器名称
        header('Cache-Control: max-age=0');//禁止缓存
        echo $this->content;
        exit;
    }
}
