<?php

namespace globus\tilda;

use globus\tilda\models\TildaImage;
use globus\tilda\models\TildaPage;
use globus\tilda\models\TildaScript;
use globus\tilda\models\TildaStyle;

class TildaExportPage
{
    const JS_PATH = '/js/';
    const CSS_PATH = '/css/';
    const IMG_PATH = '/img/';

    public $pageID;
    public $projectID;
    public $assetsPath;
    public $title;
    public $alias;
    private $html;
    private $img;
    private $css;
    private $js;

    public function __construct(Array $response, $assetsPath, $assetsUrl)
    {
        $this->assetsPath = $assetsPath;
        $this->assetsUrl = $assetsUrl;

        $this->pageID = isset($response['id']) ? $response['id'] : null;
        $this->projectID = isset($response['projectid']) ? $response['projectid'] : null;
        $this->title = isset($response['title']) ? $response['title'] : null;
        $this->alias = isset($response['alias']) ? $response['alias'] : null;
        $this->html = isset($response['html']) ? $response['html'] : null;
        $this->img = isset($response['images']) ? $response['images'] : [];
        $this->css = isset($response['css']) ? $response['css'] : [];
        $this->js = isset($response['js']) ? $response['js'] : [];

        $this->savePage();
    }

    private function savePage()
    {
        $page = TildaPage::findOne([
            'page_id' => $this->pageID,
            'project_id' => $this->projectID
        ]) ? : new TildaPage();

        $page->setAttributes([
            'page_id' => $this->pageID,
            'project_id' => $this->projectID,
            'published' => true,
            'title' => $this->title,
            'html' => $this->html,
            'alias' => $this->alias,
        ]);

        if ($page->save()) {
            $this->saveCSS($page);
            $this->saveJS($page);
            $this->saveIMG($page);
        }
    }

    private function saveIMG($savedPage)
    {
        $data = [];
        $replaceFrom = [];
        $replaceTo = [];
        $savedPage->unlinkAll('tildaImages', true);
        $basePath = $this->preparePath(self::IMG_PATH);

        foreach ($this->img as $item) {
            $path = $basePath . $item['to'];
            $url = $this->prepareUrl(self::IMG_PATH) . $item['to'];

            $data[] = [
                'tilda_page_id' => $savedPage->id,
                'source_url' => $item['from'],
                'path' => $url,
                'name' => $item['to']
            ];

            $this->fetchFile($item['from'], $path);

            $replaceFrom[] = $item['to'];
            $replaceTo[] = $url;
        }
        $savedPage->replaceImg($replaceFrom, $replaceTo);
        $savedPage->save();

        return \Yii::$app->db->createCommand()
            ->batchInsert(TildaImage::tableName(), ['tilda_page_id', 'source_url', 'path', 'name'], $data)
            ->execute();
    }

    private function saveCSS($savedPage)
    {
        $data = [];
        $savedPage->unlinkAll('tildaStyles', true);
        $basePath = $this->preparePath(self::CSS_PATH);

        foreach ($this->css as $item) {
            $path = $basePath . $item['to'];

            $data[] = [
                'tilda_page_id' => $savedPage->id,
                'source_url' => $item['from'],
                'path' => $this->prepareUrl(self::CSS_PATH) . $item['to'],
                'name' => $item['to']
            ];

            $this->fetchFile($item['from'], $path);
        }

        return \Yii::$app->db->createCommand()
            ->batchInsert(TildaStyle::tableName(), ['tilda_page_id', 'source_url', 'path', 'name'], $data)
            ->execute();
    }

    private function saveJS($savedPage)
    {
        $data = [];
        $savedPage->unlinkAll('tildaScripts', true);
        $basePath = $this->preparePath(self::JS_PATH);

        foreach ($this->js as $item) {
            $path = $basePath . $item['to'];

            $data[] = [
                'tilda_page_id' => $savedPage->id,
                'source_url' => $item['from'],
                'path' => $this->prepareUrl(self::JS_PATH) . $item['to'],
                'name' => $item['to']
            ];

            $this->fetchFile($item['from'], $path);
        }

        return \Yii::$app->db->createCommand()
            ->batchInsert(TildaScript::tableName(), ['tilda_page_id', 'source_url', 'path', 'name'], $data)
            ->execute();
    }

    private function fetchFile($sourceUrl, $destination)
    {
        try {
            file_put_contents($destination, file_get_contents($sourceUrl));
        } catch (\Exception $e) {
            \Yii::warning($e->getMessage(), 'yii2-tilda-api');
        }
    }

    private function preparePath($type)
    {
        $path = $this->assetsPath;

        if (file_exists($path) || mkdir($path, 0777, true)) {
            $path .= $this->pageID;

            if (file_exists($path) || mkdir($path, 0777, true)) {

                switch ($type) {
                    case self::IMG_PATH:
                        $path .= self::IMG_PATH;
                        break;
                    case self::JS_PATH:
                        $path .= self::JS_PATH;
                        break;
                    case self::CSS_PATH:
                        $path .= self::CSS_PATH;
                        break;
                    default:
                        break;
                }

                if (file_exists($path) || mkdir($path, 0777, true)) {
                    array_map('unlink', glob($path . "*"));

                    return $path;
                }
            }
        }

        return false;
    }

    private function prepareUrl($type)
    {
        $url = $this->assetsUrl . $this->pageID;

        switch ($type) {
            case self::IMG_PATH:
                $url .= self::IMG_PATH;
                break;
            case self::JS_PATH:
                $url .= self::JS_PATH;
                break;
            case self::CSS_PATH:
                $url .= self::CSS_PATH;
                break;
            default:
                break;
        }

        return $url;
    }
}