<?php


class HurryHandleException extends DevFixException
{

    public $url;

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public static function getFixRole()
    {
        return \App\Models\Roles::NAME_WORKFLOW_MANAGER;
    }

}