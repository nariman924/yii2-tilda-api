<?php

namespace globus\tilda;

use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\httpclient\Client;

class TildaApi extends Component
{
    const API_BASE_URL = 'http://api.tildacdn.info';
    const API_STATUS_SUCCESS = 'FOUND';
    const API_STATUS_ERROR = 'ERROR';

    //Список проектов
    const GET_PROJECT_LIST = '/v1/getprojectslist';
    //Информация о проекте
    const GET_PROJECT_INFO = '/v1/getproject';
    //Информация о проекте для экспорта
    const GET_PROJECT_INFO_EXPORT = '/v1/getprojectexport';
    //Список страниц в проекте
    const GET_PAGE_LIST = '/v1/getpageslist';
    //Информация о странице (+ body html-code)
    const GET_PAGE_INFO = '/v1/getpage';
    //Информация о странице (+ fullpage html-code)
    const GET_PAGE_INFO_FULL = '/v1/getpagefull';
    //Информация о странице для экспорта (+ body html-code)
    const GET_PAGE_EXPORT = '/v1/getpageexport';
    //Информация о странице для экспорта (+ fullpage html-code)
    const GET_PAGE_EXPORT_FULL = '/v1/getpagefullexport';

    /** @var  string */
    public $imagePath;
    /** @var  string */
    public $cssPath;
    /** @var  string */
    public $jsPath;
    /** @var  string */
    public $htmlPath = '';
    /** @var  string */
    public $publicKey;
    /** @var  string */
    public $secretKey;
    /** @var  TildaExportPage */
    public $pageObj;
    /** @var  Client */
    public $client;

    public function init()
    {
        $this->client = new Client([
            'transport' => 'yii\httpclient\CurlTransport',
            'baseUrl' => self::API_BASE_URL
        ]);

        if (!$this->publicKey) {
            throw new InvalidConfigException("publicKey can't be empty!");
        }
        if (!$this->secretKey) {
            throw new InvalidConfigException("secretKey can't be empty!");
        }
    }

    public function getPage($pageID)
    {
        $request = $this->client->createRequest()
            ->setMethod('get')
            ->setUrl(self::GET_PAGE_EXPORT)
            ->setData([
                'publickey' => $this->publicKey,
                'secretkey' => $this->secretKey,
                'pageID' => $pageID,
            ])->send();

        if ($request->isOk) {
            var_dump($request->data);
        }
    }
}
