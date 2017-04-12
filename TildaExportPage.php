<?php
/**
 * Created by PhpStorm.
 * User: nariman
 * Date: 12.04.17
 * Time: 10:34
 */

namespace globus\tilda;


class TildaExportPage
{
    public $pageID;
    public $title;
    private $html;
    private $img;
    private $css;
    private $js;

    public function __construct(Array $response)
    {
        $this->pageID = isset($response['id']) ?  : null;
        $this->title = isset($response['title']) ?  : null;
        $this->html = isset($response['html']) ?  : null;
        $this->img = isset($response['images']) ?  : null;
        $this->css = isset($response['css']) ?  : null;
        $this->js = isset($response['js']) ?  : null;
    }

    public function getHTML()
    {

    }

    public function saveIMG()
    {

    }

    public function saveCSS()
    {

    }

    public function saveJS()
    {

    }
}